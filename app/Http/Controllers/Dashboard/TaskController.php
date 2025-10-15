<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\NotificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Notification;
use App\Models\Task;
use App\Services\JobService;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskController extends Controller
{

    public function __construct(
        private JobService $jobService,
        private TaskService $taskService
    ) {
        $this->middleware('can:read_task')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_task')->only('create', 'store');
        $this->middleware('can:update_task')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.task.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Task::withTrashed()->with([
                'employees' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                },
            ])->when($request->employee_id, function ($query) use ($request) {
                $query->whereRelation('employeeTasks', 'employee_id', $request->employee_id);
            })->latest()
        )->addColumn('user_data', function ($item) {
            $users = [];
            foreach ($item->employees as $index => $employee) {
                $user = $employee->user;
                if ($index == 4)
                    break;
                $image = $user->getFirstMedia('profile') ? $user->getFirstMediaUrl('profile') : asset('assets/img/illustrations/NoImage.png');
                $users[$index]['name'] = $user->name;
                $users[$index]['image'] = $image;
                $users[$index]['type_id'] = $user->type_id;
            }
            return $users;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        //
    }

    public function create()
    {
        return view('dashboard.task.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->afterValidation();
        $task = $this->taskService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('task.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, ShowTaskRequest $request)
    {
        $data = $request->afterValidation($id);
        $task = $this->taskService->show($id, $data);
        return view('dashboard.task.show', compact('task'));
    }

    public function edit(string $id)
    {
        $task = $this->taskService->show($id);
        return view('dashboard.task.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $task = $this->taskService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('task.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
