<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Http\Controllers\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Repositories\UsersRepository;
use Codeception\Test\Unit;
use Illuminate\Support\Facades\Hash;

class UsersRepositoryTest extends Unit
{
    public function testCreateFromRequest(): void
    {
        $request = new RegisterRequest([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $repository = new UsersRepository;
        $user = $repository->createFromRequest($request);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
