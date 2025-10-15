<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Class RoleService.
 */
class RoleService
{
    public function getAllRoles($search = null)
    {
        return Role::query()->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%");
        })->latest()->get();
    }

    public function store($data)
    {
        $role = Role::create(['name' => $data['name']]);
        if (array_key_exists('permissions', $data))
            $permissions = array_map('intval', $data['permissions']);
        $role->syncPermissions($permissions);
        return true;
    }

    public function edit($id)
    {
        $permissions = Permission::all();
        $permissions = $permissions->groupBy('group');
        $role = Role::find($id);
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $result['role'] = $role;
        $result['permissions'] = $permissions;
        $result['rolePermissions'] = $rolePermissions;
        return $result;
    }

    public function update(array $data, int $id)
    {
        $role = Role::find($id);
        $role->update(['name' => $data['name']]);
        if (array_key_exists('permissions', $data)) {
            $permissions = array_map('intval', $data['permissions']);
            $role->syncPermissions($permissions);
        }
        return 1;
    }
}
