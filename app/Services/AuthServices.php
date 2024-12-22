<?php

namespace App\Services;

use App\Dto\Auth\AuthLoginDto;
use App\Dto\Auth\AuthRegisterDto;
use App\Dto\DefaultResponseDto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    public function register(AuthRegisterDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $payload->name,
                'email' => $payload->email,
                'password' => bcrypt($payload->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return new DefaultResponseDto(
                status: true,
                message: 'User registered successfully',
                data: [
                    'user' => $user,
                    'token' => $token,
                ],
            );

        } catch (\Exception $e) {
            report($e);

            DB::rollBack();

            return new DefaultResponseDto(
                status: false,
                message: 'Failed to register user | ' . $e->getMessage(),
            );
        }
    }

    public function login(AuthLoginDto $payload): DefaultResponseDto
    {
        try {
            $user = User::where('email', $payload->email)->first();

            if (!$user || !Hash::check($payload->password, $user->password)) {
                return new DefaultResponseDto(
                    status: false,
                    message: 'Invalid credentials',
                );
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return new DefaultResponseDto(
                status: true,
                message: 'User logged in successfully',
                data: [
                    'user' => $user,
                    'token' => $token,
                ],
            );

        } catch (\Exception $e) {
            report($e);

            return new DefaultResponseDto(
                status: false,
                message: 'Failed to login user | ' . $e->getMessage(),
            );
        }
    }
}
