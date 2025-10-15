<?php

namespace App\Services;

use App\Http\Resources\UserInfoResource;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Traits\MessagingTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    use MessagingTrait;

    function __construct(
        protected UserService $userService,
        protected CustomerService $customerService
    ) {}

    public function register(array $data)
    {
        if (isset($data['verified']))
            $data['email_verified_at'] = now();
        DB::beginTransaction();
        $user = $this->userService->store($data);

        if (isset($data['image'])) {
            $user->addMedia($data['image'])->toMediaCollection('profile');
        }
        $this->customerService->store($user, ['city_id' => $data['city_id'], 'address' => $data['address'], 'branch_id' => $data['branch_id']]);
        $code = '11111' ?? rand(1111, 9999);
        Cache::put($data['phone'] . $code, true, now()->addMinutes(10));
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new SendOtpMail($code));
            } catch (\Exception $e) {
                //
            }
        }
        if ($user->phone) {
            // $this->messagingService->sendSMS($user->phone, $code);
        }

        DB::commit();
        $user["token"] = $user->createToken(env('APP_KEY'))->plainTextToken;
        return $user;
    }

    public function reset_password($data)
    {
        $user = User::where('phone', $data['phone'])->first();
        $code = '11111' ?? rand(1111, 9999);
        Cache::put($data['phone'] . $code, true, now()->addMinutes(10));
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new SendOtpMail($code));
            } catch (\Exception $e) {
                //
            }
        }
        if ($user->phone) {
            // $this->messagingService->sendSMS($user->phone, $code);
        }
        return $user;
    }

    public function reset_password_confirm($data)
    {
        $user = User::where('phone', $data['phone'])->first();
        $code = '11111' ?? rand(1111, 9999);
        Cache::put($data['phone'] . $code, true, now()->addMinutes(10));
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new SendOtpMail($code));
            } catch (\Exception $e) {
                //
            }
        }
        if ($user->phone) {
            // $this->messagingService->sendSMS($user->phone, $code);
        }
        return $user;
    }

    public function sendOtp(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();
        $user->update(['code' => '11111']);
        return $user;
    }

    public function apiLogin($data)
    {
        $user = User::query()->where('phone', $data['phone'])->first();
        $user['token'] = $user->createToken(env('APP_KEY'))->plainTextToken;
        return $user;
    }

    public function verify(array $data)
    {
        $user = User::when(isset($data['phone']), function ($query) use ($data) {
            $query->where('phone', $data['phone']);
        })->when(isset($data['email']), function ($query) use ($data) {
            $query->where('email', $data['email']);
        })->first();
        if (isset($data['notification_token'])) {
            $user->update(['notification_token' => $data['notification_token']]);
        }
        $user->update(['email_verified_at' => now()]);
        $user["token"] = $user->createToken('api_token')->plainTextToken;
        Cache::forget($data['phone'] . $data['otp']);
        return $user;
    }
    public function resend(array $data)
    {
        $user = User::when(isset($data['phone']), function ($query) use ($data) {
            $query->where('phone', $data['phone']);
        })->when(isset($data['email']), function ($query) use ($data) {
            $query->where('email', $data['email']);
        })->first();
        $code = '11111' ?? rand(11111, 99999);
        Cache::put($data['phone'] . $code, true, now()->addMinutes(5));
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new SendOtpMail($code));
            } catch (\Exception $e) {
                //
            }
        }
        if ($user->phone) {
            // $this->messagingService->sendSMS($user->phone, $code);
        }
        return $user;
    }

    public function resend_code(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();
        if ($user) {
            $user->update(['code' => '11111']);
            return $user;
        }
        return [];
    }

    public function login(array $data)
    {
        $user = User::when(isset($data['phone']), function ($query) use ($data) {
            $query->where('phone', $data['phone']);
        })->when(isset($data['email']), function ($query) use ($data) {
            $query->where('email', $data['email']);
        })->first();
        $code = '11111' ?? rand(11111, 99999);
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new SendOtpMail($code));
            } catch (\Exception $e) {
                //
            }
        }
        if ($user->phone) {
            // $this->messagingService->sendSMS($user->phone, $code);
        }
        if (isset($data['notification_token'])) {
            $user->update(['notification_token' => $data['notification_token']]);
        }
        Cache::put($data['phone'] . $code, true, now()->addMinutes(5));
        $user["token"] = $user->createToken(env('APP_KEY'))->plainTextToken;
        return $user;
    }

    public function all($searchQuery = null)
    {
        //
    }

    public function show() {}

    public function updateOrCreate($data): User
    {
        return User::updateOrCreate($data['id'] ?? null, $data);
    }

    public function destroy($id): bool
    {
        return User::whereId($id)->destroy();
    }
}
