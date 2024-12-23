<?php

namespace App\Traits;

use App\Dto\DefaultResponseDto;

trait JsonResponseTrait
{
    private function successResponse(string $message, array $data = []): DefaultResponseDto
    {
        return new DefaultResponseDto(
            status: true,
            message: $message,
            data: $data
        );
    }

    private function errorResponse(string $message): DefaultResponseDto
    {
        return new DefaultResponseDto(
            status: false,
            message: $message
        );
    }
}
