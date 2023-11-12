<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Status;
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
    public function getUserParentTasks(IndexTasksRequest $request): LengthAwarePaginator
    {
        $qb = DB::table('tasks')
            ->whereNull('parent_id') //better place
            ->where('user_id', '=', auth()->user()->id);

        if ($searchTerm = $request->validated('filters.search')) {
            $searchIds = DB::table('tasks')
                ->select('id')
                ->whereNull('parent_id') //better place
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

        return $qb->paginate();
    }

    public function createUserTask(CreateTaskRequest $request): Task
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
            dd('TODO save failed, handle me!');
        }

        return $task;
    }

    public function updateUserTask(Task $task, UpdateTaskRequest $request): Task
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
            dd('TODO cant update task, handle me');
        }

        return $task;
    }

    public function completeUserTask(Task $task): Task
    {
        if (is_null($task->parent_id)) {
            $todoChildTasksCount = DB::table('tasks')
                ->where('parent_id', '=', $task->id)
                ->where('status', '=', Status::TODO->value)
                ->count();
            if ($todoChildTasksCount > 0) {
                dd('PARENT WIT UNFINISHED CHILDREN! TODO HANDLE ME!');
            }
        }
        $task->status = Status::DONE->value;
        $task->completed_at = Carbon::now();

        if (!$task->update()) {
            dd('TODO cant compete task, handle me');
        }
        return $task;
    }
}
