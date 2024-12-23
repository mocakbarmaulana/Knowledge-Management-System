<?php

namespace App\Contracts\Auth;

interface AuthServiceInterface
{
    public function register(\App\Dto\Auth\AuthRegisterDto $payload): \App\Dto\DefaultResponseDto;

    public function login(\App\Dto\Auth\AuthLoginDto $payload): \App\Dto\DefaultResponseDto;
}
