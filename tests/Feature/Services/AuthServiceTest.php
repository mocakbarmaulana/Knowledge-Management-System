<?php

use Illuminate\Support\Facades\DB;

describe('AuthServices_register', function () {
    $payload = new \App\Dto\Auth\AuthRegisterDto(
        name: 'John Doe',
        email: 'john@test.com',
        password: 'password',
    );

    it('should register a new user successfully', function () use ($payload) {
        // Mock User
        $mockUserModel = Mockery::mock('alias:' . \App\Models\User::class)
            ->makePartial();
        $mockUserModel->shouldReceive('create')
            ->once()
            ->andReturnUsing(
                function () {
                    $mockUser = Mockery::mock('alias:' . \App\Models\User::class)
                        ->makePartial();
                    $mockUser->shouldReceive('createToken')
                        ->once()
                        ->andReturn((object) ['plainTextToken' => 'token'])
                        ->getMock();


                    return $mockUser;
                }
            );

        // Mock DB
        DB::shouldReceive('beginTransaction')
            ->once()
            ->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $authServices = new \App\Services\AuthService();
        $result = $authServices->register($payload);

        expect($result)->toBeInstanceOf(\App\Dto\DefaultResponseDto::class);
        expect($result->status)->toBeTrue();
        expect($result->message)->toBe('User registered successfully');
        expect($result->data)->toBeArray();
        expect($result->data['user'])->toBeInstanceOf(\App\Models\User::class);
        expect($result->data['token'])->toBe('token');
    });

    it('should return an error response when an exception is thrown', function () use ($payload) {
        // Mock DB
        DB::shouldReceive('beginTransaction')
            ->once()
            ->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        // Mock User
        Mockery::mock('alias:' . \App\Models\User::class)
            ->shouldReceive('create')
            ->andThrow(new \Exception('An error occurred'));

        $authServices = new \App\Services\AuthService();
        $result = $authServices->register($payload);

        expect($result)->toBeInstanceOf(\App\Dto\DefaultResponseDto::class);
        expect($result->status)->toBeFalse();
        expect($result->message)->toBeString();
        expect($result->data)->toBeNull();
    });
});

describe('AuthServices_login', function () {
    $payload = new \App\Dto\Auth\AuthLoginDto(
        email: 'john@test.com',
        password: 'password',
    );

    it('should login a user successfully', function () use ($payload) {
        // Mock User
        $mockUserModel = Mockery::mock('alias:' . \App\Models\User::class)
            ->makePartial();
        $mockUserModel->shouldReceive('where->first')
            ->once()
            ->andReturnUsing(function(){
                $mockUser = Mockery::mock('alias:' . \App\Models\User::class)
                    ->makePartial();
                $mockUser->password = bcrypt('password');
                $mockUser->shouldReceive('createToken')
                    ->once()
                    ->andReturn((object) ['plainTextToken' => 'token'])
                    ->getMock();
                return $mockUser;
            });

        $authServices = new \App\Services\AuthService();
        $result = $authServices->login($payload);

        expect($result)->toBeInstanceOf(\App\Dto\DefaultResponseDto::class);
        expect($result->status)->toBeTrue();
        expect($result->message)->toBe('User logged in successfully');
        expect($result->data)->toBeArray();
        expect($result->data['user'])->toBeInstanceOf(\App\Models\User::class);
        expect($result->data['token'])->toBe('token');
    });

    it('should login a user invalid credentials', function () use ($payload) {
        // Mock User
        $mockUserModel = Mockery::mock('alias:' . \App\Models\User::class)
            ->makePartial();
        $mockUserModel->shouldReceive('where->first')
            ->once()
            ->andReturnNull();

        $authServices = new \App\Services\AuthService();
        $result = $authServices->login($payload);

        expect($result)->toBeInstanceOf(\App\Dto\DefaultResponseDto::class);
        expect($result->status)->toBeFalse();
        expect($result->message)->toBe('Invalid credentials');
        expect($result->data)->toBeNull();
    });

    it('should login a user error exception', function () use ($payload) {
        // Mock User
        $mockUserModel = Mockery::mock('alias:' . \App\Models\User::class)
            ->makePartial();
        $mockUserModel->shouldReceive('where->first')
            ->andThrow(new \Exception('An error occurred'));

        $authServices = new \App\Services\AuthService();
        $result = $authServices->login($payload);

        expect($result)->toBeInstanceOf(\App\Dto\DefaultResponseDto::class);
        expect($result->status)->toBeFalse();
        expect($result->message)->toBe('Failed to login | An error occurred');
        expect($result->data)->toBeNull();
    });
});
