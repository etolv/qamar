<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Class AdminService.
 */
class AdminService
{
    public function store($data)
    {
        $data['password'] = Hash::make($data['password']);
        $role = Role::find($data['role_id']);
        unset($data['role_id']);
        $user = User::create($data);
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
        }
        $user->assignRole($role);
        $admin = new Admin();
        $admin->user()->associate($user);
        $admin->save();
        $user->account()->associate($admin);
        $user->save();
        return $admin;
    }

    public function update($data, $id)
    {
        $user = User::withTrashed()->find($id);
        $data['password'] = $data['password'] ? Hash::make($data['password']) : $user->password;
        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            $user->roles()->detach();
            $user->assignRole($role);
        }
        $user->update($data);
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
        }
        return $user;
    }
}
