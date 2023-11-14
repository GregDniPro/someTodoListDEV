<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Controllers\Requests\Auth\RegisterRequest;
use App\Models\User;

class UsersRepository
{
    public function __construct(public User $user)
    {
    }

    public function createFromRequest(RegisterRequest $request): User
    {
        return $this->user->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
    }
}
