<?php

namespace App\Services;

use App\Models\Account;

/**
 * Class AccountService.
 */
class AccountService
{
    public function all($data = [], $paginated = false)
    {
        return Account::when(isset($data['search']), function ($query) use ($data) {
            $query->where('name', 'like', "%{$data['search']}%")
                ->orWhere('slug', 'like', "%{$data['search']}%");
        })->when($paginated, function ($query) {
            return $query->paginate();
        }, function ($query) {
            return $query->get();
        });
    }

    public function store($data): Account
    {
        return Account::create($data);
    }

    public function update($data, $id)
    {
        return Account::where('id', $id)->update($data);
    }

    public function fromSlug($slug): Account | null
    {
        return Account::where('slug', $slug)->first();
    }

    public function show($id)
    {
        return Account::find($id);
    }
}
