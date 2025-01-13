<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 *
 * @group Auth
 *
 * APIs for managing authentication
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use JsonResponseTrait;

    public function __construct(
        private readonly \App\Services\AuthServices $authServices
    )
    {

    }

    /**
     * Register a new user
     *
     * @param RegisterRequest $registerRequest
     * @return JsonResponse
     */
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $payload = new \App\Dto\Auth\AuthRegisterDto(
            name: $registerRequest->name,
            email: $registerRequest->email,
            password: $registerRequest->password,
        );

        $result = $this->authServices->register($payload);

        return $this->buildResponse($result, $result->status ? 201 : 400);
    }

    /**
     * Login a user
     *
     * @param LoginRequest $loginRequest
     * @return JsonResponse
     */
    public function login(LoginRequest $loginRequest): JsonResponse
    {
        $payload = new \App\Dto\Auth\AuthLoginDto(
            email: $loginRequest->email,
            password: $loginRequest->password,
        );

        $result = $this->authServices->login($payload);

        return $this->buildResponse($result, $result->status ? 200 : 401);
    }
}
