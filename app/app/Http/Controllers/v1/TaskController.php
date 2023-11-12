<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

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
    ) {}

    public function index(IndexTasksRequest $request): TaskResource
    {
        $paginated = $this->tasksRepository->getUserParentTasks(
            $request
        );

        return new TaskResource($paginated);
    }

    public function show(Task $task): TaskResource
    {
        if (is_null($task->parent_id)) {
            $task->load('children');
        } else {
            $task->load('parent');
        }

        return new TaskResource($task);
    }

    public function store(CreateTaskRequest $request): TaskResource
    {
        $task = $this->tasksRepository->createUserTask(
            $request
        );

        return new TaskResource($task);
    }

    public function update(Task $task, UpdateTaskRequest $request)
    {
        $task = $this->tasksRepository->updateUserTask(
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
            dd('TODO handle me! cant delete');
        }
        return response()->json([], 204);
    }
}
