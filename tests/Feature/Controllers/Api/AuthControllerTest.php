<?php

describe('AuthController_Register', function () {
    it('should register a new user successfull', function () {
        $responseData = [
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@test.com',
            ],
            'token' => 'token',
        ];

        /**  @var \App\Services\AuthService|\Mockery\MockInterface $authServices */
        $authServices = Mockery::mock(\App\Services\AuthService::class);
        $authServices->shouldReceive('register')->once()->andReturn(
            new \App\Dto\DefaultResponseDto(
                status: true,
                message: 'User registered successfully',
                data: $responseData,
            )
        );

        $controller = new \App\Http\Controllers\Api\AuthController($authServices);

        $result = $controller->register(new \App\Http\Requests\Auth\RegisterRequest([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));

        expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($result->getStatusCode())->toBe(201);
        expect($result->getData(true))->toBe([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => $responseData,
        ]);
    });
});

describe('AuthController_Login', function () {
    it('should login a user successfull', function () {
        $responseData = [
            'user' => [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@test.com',
            ],
            'token' => 'token',
        ];

        /**  @var \App\Services\AuthService|\Mockery\MockInterface $authServices */
        $authServices = Mockery::mock(\App\Services\AuthService::class);
        $authServices->shouldReceive('login')->once()->andReturn(
            new \App\Dto\DefaultResponseDto(
                status: true,
                message: 'User logged in successfully',
                data: $responseData,
            )
        );

        $controller = new \App\Http\Controllers\Api\AuthController($authServices);

        $result = $controller->login(new \App\Http\Requests\Auth\LoginRequest([
            'email' => 'john@test.com',
            'password' => 'password',
        ]));

        expect($result)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($result->getStatusCode())->toBe(200);
        expect($result->getData(true))->toBe([
            'status' => true,
            'message' => 'User logged in successfully',
            'data' => $responseData,
        ]);
    });
});
