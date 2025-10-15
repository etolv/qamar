<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JobController extends Controller
{

    public function __construct(protected JobService $jobService)
    {
        $this->middleware('can:read_job')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_job')->only('store', 'create');
        $this->middleware('can:update_job')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.job.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Job::withTrashed()->latest()
        )->editColumn('section', function ($item) {
            return $item->section->name;
        })->addColumn('employees_count', function ($item) {
            return $item->employees()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $jobs = $this->jobService->all($request->q);
        return response()->json(['data' => $jobs]);
    }

    public function create()
    {
        return view('dashboard.job.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateJobRequest $request)
    {
        $data = $request->validated();
        $job = $this->jobService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('job.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $job = $this->jobService->show($id);
        return view('dashboard.job.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreJobRequest $request, string $id)
    {
        $data = $request->validated();
        $job = $this->jobService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('job.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
