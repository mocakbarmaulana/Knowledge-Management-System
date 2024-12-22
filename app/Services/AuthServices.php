<?php

namespace App\Services;

use App\Dto\Auth\AuthRegisterDto;
use App\Dto\DefaultResponseDto;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
                message: 'Failed to register user',
            );
        }
    }
}
