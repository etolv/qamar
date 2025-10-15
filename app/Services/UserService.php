<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Class UserService.
 */
class UserService
{
    public function all($search = null)
    {
        return User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->paginate(Config::get('app.perPage'));
    }

    public function show($id)
    {
        // return User::with('account', 'contacts')->find($id);
        return User::with([
            'account' => function ($query) {
                $query->withTrashed();
            }
        ])->find($id);
    }

    public function store(array $request): User
    {
        $user = User::create(Arr::only($request, ['name', 'email', 'phone', 'password', 'email_verified_at', 'notification_token']));
        return $user;
    }

    public function update($data, $id): User
    {
        $user = User::find($id);
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
            unset($data['image']);
        }
        if (isset($data['password']) && $data['password'])
            $data['password'] = Hash::make($data['password']);
        else
            $data['password'] = $user->password;
        // if ($data['phone'] != $user->phone) {
        //     $data['email_verified_at'] = null;
        //     $data['code'] = '1111';
        // }
        // if ($data['dial_code'] != $user->dial_code) {
        //     $data['email_verified_at'] = null;
        //     $data['code'] = '1111';
        // }
        if (isset($data['city_id'])) {
            $user->account()->update(['city_id' => $data['city_id']]);
        }
        if (isset($data['branch_id'])) {
            $user->account()->update(['branch_id' => $data['branch_id']]);
        }
        if (isset($data['job_id'])) {
            $user->account()->update(['job_id' => $data['job_id']]);
        }
        if (isset($data['nationality_id'])) {
            $user->account()->update(['nationality_id' => $data['nationality_id']]);
        }
        if (isset($data['address'])) {
            $user->account()->update(['address' => $data['address']]);
        }
        if (isset($data['vacation_days'])) {
            $taken_days = $user->account->used_vacation_days;
            $user->account()->update(['vacation_days' => $data['vacation_days']]);
            $user->account()->update(['remaining_vacation_days' => $data['vacation_days'] - $taken_days]);
        }
        if (isset($data['role_id'])) {
            $user->roles()->detach();
            $role = Role::find($data['role_id']);
            $user->assignRole($role);
        }
        $user->update($data);
        return $user;
    }

    public function update_firebase_token($data, $user_id)
    {
        $user = User::find($user_id);
        $user->update(['notification_token' => $data['notification_token']]);
        return $user;
    }

    public function destroy()
    {
        //
    }
}
