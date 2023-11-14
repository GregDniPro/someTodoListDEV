<?php

namespace App\Repositories\Interfaces;

use App\Http\Controllers\Requests\Auth\RegisterRequest;
use App\Models\User;

interface UsersRepositoryInterface
{
    public function createFromRequest(RegisterRequest $request): User;
}
