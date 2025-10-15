<?php

namespace App\Services;

use App\Models\State;

/**
 * Class StateService.
 */
class StateService
{

    public function all()
    {
        return State::get();
    }
    public function store($data)
    {
        return State::create($data);
    }

    public function update($data, $id)
    {
        $state = State::find($id);
        $state->update($data);
        return $state;
    }
}
