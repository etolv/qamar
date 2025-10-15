<?php

namespace App\Services;

use App\Models\Job;

/**
 * Class JobService.
 */
class JobService
{
    public function all($search = null, $paginated = false)
    {
        $query = Job::when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%$search%")
                ->orWhere('section', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");
        });
        if ($paginated) {
            return $query->paginate(10);
        }
        return $query->get();
    }

    public function show($id)
    {
        return Job::with('employees')->find($id);
    }

    public function store($data)
    {
        return Job::create($data);
    }

    public function update($data, $id)
    {
        $job = Job::find($id);
        $job->update($data);
        return $job;
    }
}
