<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Exceptions\DatabaseOperationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Requests\v1\Tasks\CreateTaskRequest;
use App\Http\Controllers\Requests\v1\Tasks\IndexTasksRequest;
use App\Http\Controllers\Requests\v1\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\TasksRepository;

class TaskController extends Controller
{
    public function __construct(
        protected TasksRepository $tasksRepository
    ) {
    }

    public function index(IndexTasksRequest $request): TaskResource
    {
        $paginated = $this->tasksRepository->getUserTasks(
            $request
        );

        return new TaskResource($paginated);
    }

    public function show(Task $task): TaskResource
    {
        $task->load(['parent', 'children']);

        return new TaskResource($task);
    }

    public function store(CreateTaskRequest $request): TaskResource
    {
        $task = $this->tasksRepository->createTask(
            $request
        );

        return new TaskResource($task);
    }

    public function update(Task $task, UpdateTaskRequest $request)
    {
        $task = $this->tasksRepository->updateTask(
            $task,
            $request
        );

        return new TaskResource($task);
    }

    public function done(Task $task)
    {
        $task = $this->tasksRepository->completeUserTask(
            $task
        );

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        if (!$task->delete()) {
            throw new DatabaseOperationException(
                'Unable to delete the task. Try again later.'
            );
        }
        return response()->json([], 204);
    }
}
