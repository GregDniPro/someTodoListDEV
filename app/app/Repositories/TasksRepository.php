<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\Status;
use App\Http\Controllers\v1\Requests\CreateTaskRequest;
use App\Http\Controllers\v1\Requests\IndexTasksRequest;
use App\Http\Controllers\v1\Requests\UpdateTaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TasksRepository
{
    public function getUserTasks(IndexTasksRequest $request): LengthAwarePaginator
    {
        $qb = DB::table('tasks')
            ->where('user_id', '=', auth()->user()->id);

        if ($searchTerm = $request->validated('filters.search')) {
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
        //todo parent_id here

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
            if ($task->wasChanged('status')) { //TODO test
                match ($task->status) {
                    Status::TODO->value => ($task->completed_at = null),
                    Status::DONE->value => ($task->completed_at = Carbon::now()),
                };
            }
        }
        if (!$task->update()) {
            dd('TODO cant update task, handle me');
        }

        return $task;
    }

    public function completeUserTask(Task $task): Task
    {
        $task->status = Status::DONE->value;
        $task->completed_at = Carbon::now();
        if (!$task->update()) {
            dd('TODO cant compete task, handle me');
        }
        return $task;
    }
}
