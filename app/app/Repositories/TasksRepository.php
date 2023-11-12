<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Status;
use App\Exceptions\DatabaseOperationException;
use App\Exceptions\TaskHasUndoneChildrenException;
use App\Http\Controllers\Requests\v1\Tasks\CreateTaskRequest;
use App\Http\Controllers\Requests\v1\Tasks\IndexTasksRequest;
use App\Http\Controllers\Requests\v1\Tasks\UpdateTaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TasksRepository
{
    public function getUserTasks(IndexTasksRequest $request): LengthAwarePaginator
    {
        $qb = Task::where('user_id', '=', auth()->user()->id);

        if ($searchTerm = $request->validated('filters.search')) {
            //TODO elasticsearch
            $searchIds = DB::table('tasks')
                ->select('id')
                ->whereRaw("to_tsvector('english', title || ' ' || description) @@ to_tsquery('english', ?)", [$searchTerm])
                ->get();
            $qb->whereIn('id', $searchIds->pluck('id')->toArray());
        }

        if ($status = $request->validated('filters.status')) {
            $qb->where('status', '=', $status);
        }
        if ($priority = $request->validated('filters.priority')) {
            $qb->where('priority', '=', $priority);
        }

        if ($request->has('sort')) {
            foreach ($request->validated('sort') as $field => $direction) {
                $qb->orderBy($field, $direction);
            }
        }

        $qb->with('children');

        return $qb->paginate();
    }

    public function createTask(CreateTaskRequest $request): Task
    {
        $task = new Task($request->validated());
        $task->user_id = auth()->user()->id;
        $task->uuid = Str::uuid();

        if ($task->status == Status::DONE->value) {
            $task->completed_at = Carbon::now();
        }
        if ($parentId = $request->validated('parent_id')) {
            $task->parent_id = $parentId;
        }

        if (!$task->save()) {
            throw new DatabaseOperationException('Unable to create the task. Try again later.');
        }

        return $task;
    }

    public function updateTask(Task $task, UpdateTaskRequest $request): Task
    {
        if ($title = $request->validated('title')) {
            $task->title = $title;
        }
        if ($priority = $request->validated('priority')) {
            $task->priority = $priority;
        }
        if ($description = $request->validated('description')) {
            $task->description = $description;
        }
        if ($status = $request->validated('status')) {
            $task->status = $status;
            if ($task->getOriginal('status') !== $status) {
                match ($task->status) {
                    Status::TODO->value => ($task->completed_at = null),
                    Status::DONE->value => ($task->completed_at = Carbon::now()),
                };
            }
        }
        if ($parentId = $request->validated('parent_id')) {
            $task->parent_id = $parentId;
        }

        if (!$task->update()) {
            throw new DatabaseOperationException('Unable to update the task. Try again later.');
        }

        return $task;
    }

    public function completeUserTask(Task $task): Task
    {
        if (is_null($task->parent_id)) {
            //TODO rework to recursive search to check if all its possible children has done status
            $undoneChildTasksCount = DB::table('tasks')
                ->where('parent_id', '=', $task->id)
                ->where('status', '=', Status::TODO->value)
                ->count();
            if ($undoneChildTasksCount > 0) {
                throw new TaskHasUndoneChildrenException(
                    'Cannot mark task as done while dependent tasks are in "todo" status.'
                );
            }
        }
        $task->status = Status::DONE->value;
        $task->completed_at = Carbon::now();

        if (!$task->update()) {
            throw new DatabaseOperationException(
                'Unable to complete the task. Try again later.'
            );
        }
        return $task;
    }
}
