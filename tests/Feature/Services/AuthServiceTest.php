<?php

use Illuminate\Support\Facades\DB;

describe('AuthServices_register', function() {
    $payload = new \App\Dto\Auth\AuthRegisterDto(
        name: 'John Doe',
        email: 'john@test.com',
        password: 'password',
    );

    it('should register a new user successfully', function() use ($payload) {

        // Mock User
        Mockery::mock('alias:' . \App\Models\User::class)
            ->shouldReceive('create')
            ->andReturn((object) [
                'name' => $payload->name,
                'email' => $payload->email,
            ]);

        // Mock DB
        DB::shouldReceive('beginTransaction')
            ->once()
            ->andReturnNull();


        $authServices = new \App\Services\AuthServices();
        $result = $authServices->register($payload);

        dd($result);

        expect($result->status)->toBeTrue();
        expect($result->message)->toBe('User registered successfully');
    });
});
