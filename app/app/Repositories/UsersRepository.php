<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\Controllers\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Str;

class UsersRepository
{
    public function createFromRequest(RegisterRequest $request): User
    {
        return User::create([
            'uuid' => Str::uuid(),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
    }
}
