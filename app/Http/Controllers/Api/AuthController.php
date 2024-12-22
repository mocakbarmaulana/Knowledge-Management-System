<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthServices $authServices,
    )
    {

    }

    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $payload = new \App\Dto\Auth\AuthRegisterDto(
            name: $registerRequest->name,
            email: $registerRequest->email,
            password: $registerRequest->password,
        );

        $result = $this->authServices->register($payload);

        return response()->json($result, $result->status ? 201 : 400);
    }

    public function login(LoginRequest $loginRequest): JsonResponse
    {
        $payload = new \App\Dto\Auth\AuthLoginDto(
            email: $loginRequest->email,
            password: $loginRequest->password,
        );

        $result = $this->authServices->login($payload);

        return response()->json($result, $result->status ? 200 : 401);
    }
}
