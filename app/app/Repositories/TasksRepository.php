<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Controllers\v1\Requests\GetTasksRequest;
use App\Models\User;
use DB;
use Illuminate\Support\Collection;

class TasksRepository
{
    public function getUserTasks(User $user, GetTasksRequest $request): Collection
    {
        $qb = DB::table('tasks')
            ->where('user_id', '=', $user->id);

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

        return $qb->get();
    }
}
