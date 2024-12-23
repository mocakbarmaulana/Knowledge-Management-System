<?php

namespace App\Services;

use App\Dto\Auth\AuthLoginDto;
use App\Dto\Auth\AuthRegisterDto;
use App\Dto\DefaultResponseDto;
use App\Models\User;
use App\Traits\JsonResponseTrait;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthServices
{
    use JsonResponseTrait;

    /**
     * Register a new user
     *
     * @param AuthRegisterDto $payload
     * @return DefaultResponseDto
     */
    public function register(AuthRegisterDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $payload->name,
                'email' => $payload->email,
                'password' => bcrypt($payload->password),
            ]);

            $token = $this->generateToken($user);

            DB::commit();

            return $this->successResponse(
                'User registered successfully',
                [
                    'user' => $user,
                    'token' => $token,
                ],
            );
        } catch (QueryException $e) {
            report($e);

            Log::error('Query error during registration: ' . $e->getMessage());

            DB::rollBack();

            return $this->errorResponse("Failed to register user | {$e->getMessage()}");
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during registration: ' . $e->getMessage());

            DB::rollBack();

            return $this->errorResponse("Failed to register user | {$e->getMessage()}");
        }
    }

    /**
     * Login a user
     *
     * @param AuthLoginDto $payload
     * @return DefaultResponseDto
     */
    public function login(AuthLoginDto $payload): DefaultResponseDto
    {
        try {
            $user = User::where('email', $payload->email)->first();

            if (!$user || !Hash::check($payload->password, $user->password)) {
                return $this->errorResponse('Invalid credentials');
            }

            $token = $this->generateToken($user);

            return $this->successResponse(
                'User logged in successfully',
                [
                    'user' => $user,
                    'token' => $token,
                ],
            );
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during login: ' . $e->getMessage());

            return $this->errorResponse("Failed to login | {$e->getMessage()}");
        }
    }

    /**
     * Generate token for user
     *
     * @param User $user
     * @return string
     */
    private function generateToken(User $user): string
    {
        return $user->createToken(config('services.auth.token'))->plainTextToken;
    }
}
