<?php

namespace App\Services;

use App\Enums\NotificationTypeEnum;
use App\Models\Task;
use PhpParser\Node\Expr\FuncCall;

/**
 * Class TaskService.
 */
class TaskService
{


    public function __construct(private NotificationService $notificationService,) {}
    public function all($data = [], $paginated = false)
    {
        return Task::when(isset($data['employee_id']), function ($query) use ($data) {
            $query->whereHas('employees', function ($query) use ($data) {
                $query->where('id', $data['employee_id']);
            });
        })->when($paginated, function ($query) {
            return $query->paginate();
        }, function ($query) {
            return $query->get();
        });
    }

    public function show($id, $data = []): Task
    {
        return Task::find($id);
    }
    public function  store($data): Task
    {
        $task = Task::create($data);
        if (isset($data['employees'])) {
            $task->employees()->sync($data['employees']);
        }
        $notification_data = [
            'type' => NotificationTypeEnum::TASK->value,
            'name' => $task->title,
            'body' => $task->description,
            'type' => 'users',
            'user_id' => $task->user_id,
            'users' => $task->employees->pluck('user_id'),
            'data' => json_encode([
                'type' => NotificationTypeEnum::TASK->value,
                'type_id' => $task->id,
            ])
        ];
        $notification = $this->notificationService->store($notification_data);
        return $task;
    }

    public function update($data, $id)
    {
        $task = $this->show($id);
        $task->update($data);
        if (isset($data['employees'])) {
            $task->employees()->sync($data['employees']);
        }
        return $task;
    }
}
