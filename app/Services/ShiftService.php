<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class ShiftService.
 */
class ShiftService
{

    public function all($data = [])
    {
        return Shift::with([
            'employees' => function ($query) {
                $query->with([
                    'user' => fn($q) => $q->withTrashed()
                ]);
            }
        ])->when(isset($data['search']), function ($query) use ($data) {
            $query->where('name', 'like', '%' . $data['search'] . '%');
        })->paginate();
    }
    public function store($data)
    {
        DB::beginTransaction();
        $shift = Shift::create(Arr::except($data, ['employees']));
        // if (isset($data['employees'])) {
        //     $shift->employees()->attach($data['employees']);
        // }
        DB::commit();
        return $shift;
    }

    public function show($id)
    {
        $shift = Shift::withTrashed()->find($id);
        $shift->setRelation('employeeShifts', $shift->employeeShifts()->paginate(10));
        return $shift;
    }
}
