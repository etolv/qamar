<?php

namespace App\Services;

use App\Models\Card;

/**
 * Class CardService.
 */
class CardService
{
    public function store($data)
    {
        return Card::create($data);
    }

    public function update($data, $id)
    {
        $card = $this->show($id);
        $card->update($data);
        return $card;
    }

    public function show($id, $withTrashed = true)
    {
        return Card::withTrashed($withTrashed)->findOrFail($id);
    }
}
