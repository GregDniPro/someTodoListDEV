<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Requests\Auth\LoginRequest;
use App\Http\Controllers\Requests\Auth\RegisterRequest;
use App\Repositories\UsersRepository;
use Codeception\Test\Unit;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Manager;
use Tymon\JWTAuth\Http\Parser\Parser;

class AuthControllerTest extends Unit
{
    protected AuthController $authController;
    protected UsersRepository $usersRepository;

    protected function _before()
    {
        $this->initializeJWT();
        $this->usersRepository = Mockery::mock(UsersRepository::class);
        $this->authController = new AuthController($this->usersRepository);
    }

    protected function _after()
    {
        Mockery::close();
    }

    public function testLogin()
    {
        // Create a mock request object.
        $request = Mockery::mock(LoginRequest::class);
        $request->shouldReceive('input')->with('email')
            ->andReturn('test@example.com');
        $request->shouldReceive('input')->with('password')
            ->andReturn('password123');

        // Replace the JWTAuth facade with a mock instance.
        app()->instance('tymon.jwt.auth', $this->jwt);

        JWTAuth::shouldReceive('attempt')->with([
            'email' => 'test@example.com',
            'password' => 'password123',
        ])->andReturn('mocked_token');

        $response = $this->authController->login($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRegister()
    {
        // Create a mock request object for registration.
        $request = Mockery::mock(RegisterRequest::class);

        // Mock the user creation in the repository and return an instance of User.
        $user = Mockery::mock('App\Models\User');
        $this->usersRepository->shouldReceive('createFromRequest')->with($request)
            ->andReturn($user);

        // Replace the JWTAuth facade with a mock instance.
        app()->instance('tymon.jwt.auth', $this->jwt);

        // Mock the JWTAuth facade's fromUser method.
        JWTAuth::shouldReceive('fromUser')->with($user)->andReturn('mocked_token');

        $response = $this->authController->register($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testMe()
    {
        // Create a mock user object.
        $user = (object)['id' => 1, 'email' => 'test@example.com'];

        // Mock the authenticated user.
        JWTAuth::shouldReceive('user')->andReturn($user);

        $response = $this->authController->me();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRefresh()
    {
        // Mock the token refresh.
        JWTAuth::shouldReceive('refresh')->andReturn('new_token');

        $response = $this->authController->refresh();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    private function initializeJWT(): void
    {
        $manager = Mockery::mock(Manager::class);
        $parser = Mockery::mock(Parser::class);

        $this->jwt = new JWT($manager, $parser);
    }
}
