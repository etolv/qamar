<?php

namespace App\Services;

use App\Enums\NotificationTypeEnum;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as KreaitNotification;
use Kreait\Firebase\Messaging\MessageData;

class NotificationService
{

    protected $notification;
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }

    public function all($searchQuery = null, $user_id = null): LengthAwarePaginator
    {
        $notificationQuery = Notification::query();
        $notificationQuery->with('users', 'notificationUsers')
            ->when($searchQuery, function ($query) use ($searchQuery) {
                return $query->where('name', 'like', "%$searchQuery%");
            })->when($user_id, function ($query) use ($user_id) {
                $query->whereHas('users', function ($subQuery) use ($user_id) {
                    $subQuery->where('user_id', $user_id);
                });
            });
        $notifications = $notificationQuery->paginate(Config::get('app.perPage'));
        // if ($user_id) {
        //     foreach ($notifications as $notification) {
        //         $notification->notificationUsers()
        //             ->where('user_id', $user_id)
        //             ->update(['is_read' => true]);
        //     }
        // }
        return $notifications;
    }

    public function storeTask($data)
    {
        DB::beginTransaction();
        if ($data['type'] == 'users') {
            $users = $data['users'];
        } else if ($data['type'] == 'all') {
            $users = User::whereHasMorph('account', [Employee::class])->get()->pluck('id');
        } else {
            $users = User::whereHasMorph('account', [Employee::class], function ($query) use ($data) {
                $query->where('job_id', $data['type']);
            })->get()->pluck('id');
        }
        $data['type'] = NotificationTypeEnum::TASK->value;
        unset($data['users']);
        $notification = Notification::create($data);
        $notification->users()->attach($users);
        $this->sendNotification($notification);
        DB::commit();
        return $notification;
    }

    public function store($data)
    {
        DB::beginTransaction();
        if ($data['type'] == 'users') {
            $users = $data['users'];
        } else if ($data['type'] == 'employees') {
            $users = User::whereHasMorph('account', [Employee::class])->select('id')->get()->pluck('id');
        } else if ($data['type'] == 'drivers') {
            $users = User::whereHasMorph('account', [Driver::class])->select('id')->get()->pluck('id');
        } else if ($data['type'] == 'customers') {
            $users = User::whereHasMorph('account', [Customer::class])->select('id')->get()->pluck('id');
        } else if ($data['type'] == 'admins') {
            $users = User::whereHasMorph('account', [Admin::class])->select('id')->get()->pluck('id');
        } else {
            $users = User::select('id')->get()->pluck('id');
        }
        unset($data['type']);
        unset($data['users']);
        $notification = Notification::create($data);
        $notification->users()->attach($users);
        $this->sendNotification($notification);
        DB::commit();
        return $notification;
    }

    public function update($data, $id)
    {
        $notification = Notification::withTrashed()->find($id);
        $users = $data['users'];
        unset($data['users']);
        $notification->users()->detach();
        $notification->update($data);
        $notification->attach($users);
        return $notification;
    }

    public function show($id, $data = []): Notification
    {
        $notification = Notification::withTrashed()->with('users', 'notificationUsers')->find($id);
        if (isset($data['user_id'])) {
            $notification->notificationUsers()->where('user_id', $data['user_id'])->update(['is_read' => true]);
        }
        return $notification;
    }

    public function updateOrCreate($data): Notification
    {
        return Notification::updateOrCreate($data['id'] ?? null, $data);
    }

    public function destroy($id): bool
    {
        return Notification::whereId($id)->destroy();
    }

    public function sendNotification($notification_model)
    {
        try {
            $tokens = $notification_model->users->whereNotNull('notification_token')->pluck('notification_token')->toArray();
            $notification = KreaitNotification::fromArray([
                "title" => $notification_model->name,
                "body" => $notification_model->body,
            ]);

            $message = CloudMessage::new();
            $message = $message->withNotification($notification);
            if ($notification_model->data) {
                $message_data = MessageData::fromArray(json_decode($notification_model->data, true));
                $message = $message->withData($message_data);
            }
            if ($tokens)
                $response = $this->notification->sendMulticast($message, $tokens);
            return true;
        } catch (\Exception $e) {
            info("sendNOtification error");
            return false;
            dd($e);
        }

        // foreach ($notification_model->users as $user) {
        //     try {
        //         if (!$user->notification_token)
        //             continue;
        //         $message = CloudMessage::withTarget('token', $user->notification_token)
        //             ->withNotification(KreaitNotification::create($notification->name, $notification->body));
        //         if ($notification_model->data) {
        //             $data = json_decode($notification_model->data, true);
        //             $message = $message->withData($data);
        //         }
        //         $this->notification->send($message); //TODO remove comment
        //     } catch (\Exception $e) {
        //         info("sendNOtification error");
        //         return false;
        //         dd($e);
        //         return true;
        //     }
        // }
    }

    public function read($id, $user_id)
    {
        return NotificationUser::whereNotificationId($id)
            ->whereUserId($user_id)->update(['is_read' => true]);
    }

    public function updateFcmToken($notification_token, $user_id)
    {
        $user = User::find($user_id);
        $user->update(['notification_token' => $notification_token]);
        return $user;
    }

    public function registerToken(string $token, string $topic = "all", User $user = NULL)
    {
        $this->notification->subscribeToTopic($topic, $token);
    }

    public function sendNotificationForTopic(string $title, string $body, array $data = [], ?string $topic = "ALL")
    {
        try {

            $notification = KreaitNotification::fromArray([
                "title" => $title,
                "body" => $body,
                // "data" => $data
            ]);

            $message = CloudMessage::new();
            $message = $message->withTarget("topic", $topic);
            $message = $message->withNotification($notification);
            $message = $message->withData($data);
            // $message = $message->fromArray([
            //     "notification" => $notification,
            //     "data" => $data['data']
            // ]);
            // $x = $this->messaging->send($message);
            // dd($x);

            // event(new NotificationSentEvent($title, $body));
            return $this->notification->send($message);
        } catch (KreaitNotification $e) {
            // Handle the InvalidMessage exception
            //dd($e);
            return "Invalid message: ";
        }
    }
}
