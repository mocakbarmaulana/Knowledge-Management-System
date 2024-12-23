<?php

namespace App\Traits;

use App\Dto\DefaultResponseDto;
use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    private function successResponseDto(string $message, array $data = []): DefaultResponseDto
    {
        return new DefaultResponseDto(
            status: true,
            message: $message,
            data: $data
        );
    }

    private function errorResponseDto(string $message): DefaultResponseDto
    {
        return new DefaultResponseDto(
            status: false,
            message: $message
        );
    }

    /**
     * Build JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    private function buildResponse(mixed $data, int $statusCode): JsonResponse
    {
        return response()->json($data, $statusCode);
    }
}
