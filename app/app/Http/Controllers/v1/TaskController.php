<?php

declare(strict_types=1);

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Requests\GetTasksRequest;
use App\Models\Task;
use App\Repositories\TasksRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TasksRepository $tasksRepository
    ) {}

    public function index(GetTasksRequest $request): JsonResponse
    {
        $data = $this->tasksRepository->getUserTasks(
            auth()->user(),
            $request
        );

        return response()->json($data);
    }

    public function store(Request $request)
    {
        dd('post tasks');
    }

    public function show(Task $task)
    {
        dd('show task');
        //
    }

    public function update(Task $task)
    {
        dd('patch/put task');
    }

    public function destroy(Task $task)
    {
        dd('delete task');
    }

    public function done(Task $task)
    {
        //todo set to done
        dd('done');
    }
}
