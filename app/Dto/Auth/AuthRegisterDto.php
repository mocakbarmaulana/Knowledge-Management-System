<?php

namespace App\Dto\Auth;

use Spatie\LaravelData\Data;

class AuthRegisterDto extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
