<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\NotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowNotificationRequest;
use App\Http\Requests\StoreNotificationRequest;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\UserService;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Yajra\DataTables\Facades\DataTables;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService, protected UserService $userService)
    {
        $this->middleware('can:read_notification')->only('index', 'fetch', 'show');
        $this->middleware('can:create_notification')->only('store', 'create');
        $this->middleware('can:update_notification')->only('update', 'edit');
    }

    public function index()
    {
        return view('dashboard.notification.index');
    }

    public function show($id, ShowNotificationRequest $request)
    {
        $data = $request->afterValidation($id);
        $notification = $this->notificationService->show($id, $data);
        if ($notification->data && $notification->type == NotificationTypeEnum::TASK) {
            return redirect()->route('task.show', json_decode($notification->data)->type_id);
        }
        return redirect()->back();
    }

    public function read($id)
    {
        return view('dashboard.notification.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Notification::withTrashed()->with([
                'users' => fn($q) => $q->withTrashed(),
                'user' => fn($q) => $q->withTrashed()
            ])
        )->addColumn('profile_image', function ($item) {
            return $item->user?->getFirstMediaUrl('profile');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.notification.add');
    }

    public function create_user_notification($user_id)
    {
        $user = $this->userService->show($user_id);
        return view('dashboard.notification.add', compact('user'));
    }

    public function store(StoreNotificationRequest $request)
    {
        $data = $request->afterValidation();
        $notification = $this->notificationService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function edit($id)
    {
        $notification = $this->notificationService->show($id);
        return view('dashboard.notification.edit', compact('notification'));
    }

    public function update(StoreNotificationRequest $request, $id)
    {
        $data = $request->validated();
        $notification = $this->notificationService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('admin.notification.index');
    }

    public function destroy()
    {
        //
    }

    public function update_notification_token(Request $request)
    {
        $user = User::find(auth()->id());
        $user->update(['notification_token' => $request->notification_token]);
        return response()->success();
    }

    public function updateFcmToken(Request $request)
    {
        $this->notificationService->updateFcmToken($request->notification_token, auth()->id());
        return true;
    }
}
