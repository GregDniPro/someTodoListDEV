<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\LoginRequest;
use App\Http\Controllers\Requests\RegisterRequest;
use App\Repositories\UsersRepository;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        protected UsersRepository $usersRepository
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        if (!$token = JWTAuth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->usersRepository->createFromRequest($request);

        $token = JWTAuth::fromUser($user);

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(): JsonResponse
    {
        return response()->json([
            'access_token' => JWTAuth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60, //TODO move to config
        ]);
    }
}
