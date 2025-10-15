<?php

namespace App\Services;

use App\Models\OrderServiceSession;

/**
 * Class OrderServiceSessionService.
 */
class OrderServiceSessionService
{
    public function store($data)
    {
        return OrderServiceSession::create($data);
    }

    public function update($data, $id)
    {
        return OrderServiceSession::where('id', $id)->update($data);
    }
}
