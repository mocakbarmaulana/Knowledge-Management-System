<?php

namespace App\Contracts\Category;

use App\Dto\Category\CategoryCreateDto;
use App\Dto\Category\CategoryGetDto;
use App\Dto\DefaultResponseDto;

interface CategoryServiceInterface
{
    public function getCategories(CategoryGetDto $payload): DefaultResponseDto;

    public function createCategory(CategoryCreateDto $payload): DefaultResponseDto;

    public function showCategory(int $id): DefaultResponseDto;

    public function updateCategory(int $id, CategoryCreateDto $payload): DefaultResponseDto;

    public function deleteCategory(int $id): DefaultResponseDto;
}
