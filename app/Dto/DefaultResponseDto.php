<?php

namespace App\Dto;

use Spatie\LaravelData\Data;

class DefaultResponseDto extends Data
{
    public function __construct(
        public bool $status,
        public string $message,
        public mixed $data = null,
    ) {}
}
