<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

/**
 * Class DriverService.
 */
class DriverService
{
    public function __construct(protected UserService $userService) {}

    public function all($search = null, $type = null, $paginated = false, $excluded_ids = [])
    {
        $query = User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
        })->when($type, function ($query) use ($type) {
            $query->whereHas('roles', function ($query) use ($type) {
                $query->where('name', 'like', "%$type%");
            });
        })->when($excluded_ids, function ($query) use ($excluded_ids) {
            $query->whereNotIn('type_id', $excluded_ids);
        })->onlyDrivers()->latest();
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function show($id, $withes = [])
    {
        return Driver::with(array_merge(['user' => function ($query) {
            $query->withTrashed();
        }], $withes))->find($id);
    }

    public function store($data)
    {
        $user = User::create(Arr::only($data, ['password', 'name', 'phone', 'email']));
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
        }
        // TODO send password to user email
        $user->assignRole('driver');
        $driver = new Driver(Arr::except($data, ['password', 'name', 'phone', 'email', 'image']));
        $driver->user()->associate($user);
        $driver->save();
        $user->account()->associate($driver);
        $user->save();
        return $driver;
    }

    public function update($data, $id)
    {
        $driver = Driver::find($id);
        $driver->update(Arr::except($data, ['password', 'name', 'phone', 'email', 'image', 'driver_id']));
        $driver->user->update(Arr::only($data, ['name', 'phone', 'email']));
        if (isset($data['image'])) {
            $driver->user->clearMediaCollection('profile');
            $driver->user->addMedia($data['image'])->toMediaCollection('profile');
        }
        return $driver;
    }

    public function updateLocation($data)
    {
        $driver = $this->show($data['driver_id']);
        $driver->update(Arr::only($data, ['lat', 'lng']));
        return $driver;
    }
}
