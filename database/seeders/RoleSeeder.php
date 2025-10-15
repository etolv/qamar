<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'employee']);
        Role::firstOrCreate(['name' => 'driver']);
        Role::firstOrCreate(['name' => 'admin']);
        $config = config('permission_seeder.role_structure');
        $mapPermission = collect(config('permission_seeder.permissions_map'));
        foreach ($config as $key => $modules) {

            //TODO: Create a new role
            $role = Role::firstOrCreate([
                'name' => $key
            ]);
            $permissions = [];
            foreach ($modules as $module => $value) {

                foreach (explode(',', $value) as $p => $perm) {

                    $permissionValue = $mapPermission->get($perm);

                    $permissions[] = Permission::firstOrCreate([
                        'name' => $permissionValue . '_' . $module,
                        'group' => $module,
                    ])->id;

                    // $this->command->info('Creating Permission to ' . $permissionValue . ' for ' . $module);
                }
            }
            $role->permissions()->sync($permissions);
        }
    }
}
