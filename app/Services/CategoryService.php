<?php

namespace App\Services;

use App\Contracts\Category\CategoryServiceInterface;
use App\Dto\Category\CategoryCreateDto;
use App\Dto\Category\CategoryGetDto;
use App\Dto\DefaultResponseDto;
use App\Enum\MessageEnum;
use App\Models\Category;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService implements CategoryServiceInterface
{
    use JsonResponseTrait;

    protected string $notFoundMessage = 'Category not found';

    protected Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get all categories
     *
     * @param CategoryGetDto $payload
     * @return DefaultResponseDto
     */
    public function getCategories(CategoryGetDto $payload): DefaultResponseDto
    {
        try {
            $categories = $this->category->query()
                ->where('name', 'like', '%' . $payload->search . '%')
                ->orderBy('name', 'asc')
                ->paginate($payload->perPage, page: $payload->page);

            $response = [
                'content' => $categories->items(),
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                    'prev_page' => $categories->currentPage() === 1 ? null : $categories->currentPage() - 1,
                    'next_page' => $categories->perPage() * $categories->currentPage() >= $categories->total() ? null : $categories->currentPage() + 1,
                ]
            ];


            return $this->successResponseDto(
                sprintf(MessageEnum::SUCCESS_MESSAGE, 'fetch categories'),
                $response
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(sprintf(MessageEnum::ERROR_EXECPTION, 'fetching categories', $e->getMessage()));

            return $this->errorResponseDto(sprintf(MessageEnum::FAILED_MESSAGE, 'fetch categories', $e->getMessage()));
        }
    }

    /**
     * Create a new category
     *
     * @param CategoryCreateDto $payload
     * @return DefaultResponseDto
     */
    public function createCategory(CategoryCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $exists = $this->category->where('name', $payload->name)->exists();

            if ($exists) {
                return $this->errorResponseDto('Category already exists');
            }

            $category = $this->category->create([
                'name' => $payload->name,
                'description' => $payload->description,
            ]);

            DB::commit();

            return $this->successResponseDto(
                sprintf(MessageEnum::SUCCESS_MESSAGE, 'create category'),
                $category->toArray()
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(sprintf(MessageEnum::ERROR_EXECPTION, 'creating category', $e->getMessage()));

            DB::rollBack();

            return $this->errorResponseDto(sprintf(MessageEnum::FAILED_MESSAGE, 'create category', $e->getMessage()));
        }
    }

    /**
     * Show a category
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function showCategory(int $id): DefaultResponseDto
    {
        try {
            $category = $this->category->find($id);

            if (!$category) {
                return $this->errorResponseDto($this->notFoundMessage);
            }

            return $this->successResponseDto(
                sprintf(MessageEnum::SUCCESS_MESSAGE, 'fetch category'),
                $category->toArray()
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(sprintf(MessageEnum::ERROR_EXECPTION, 'fetching category', $e->getMessage()));

            return $this->errorResponseDto(sprintf(MessageEnum::FAILED_MESSAGE, 'fetch category', $e->getMessage()));
        }
    }

    /**
     * Update a category
     *
     * @param int $id
     * @param CategoryCreateDto $payload
     * @return DefaultResponseDto
     */
    public function updateCategory(int $id, CategoryCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $category = $this->category->find($id);

            if (!$category) {
                return $this->errorResponseDto($this->notFoundMessage);
            }

            $exists = $this->category->where('name', $payload->name)->where('id', '!=', $id)->exists();

            if ($exists) {
                return $this->errorResponseDto('Category name already exists');
            }

            $category->update([
                'name' => $payload->name,
                'description' => $payload->description,
            ]);

            DB::commit();

            return $this->successResponseDto(
                sprintf(MessageEnum::SUCCESS_MESSAGE, 'update category'),
                $category->toArray()
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(sprintf(MessageEnum::ERROR_EXECPTION, 'updating category', $e->getMessage()));

            DB::rollBack();

            return $this->errorResponseDto(sprintf(MessageEnum::FAILED_MESSAGE, 'update category', $e->getMessage()));
        }
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function deleteCategory(int $id): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $category = $this->category->find($id);

            if (!$category) {
                return $this->errorResponseDto($this->notFoundMessage);
            }

            $category->delete();

            DB::commit();

            return $this->successResponseDto(
                sprintf(MessageEnum::SUCCESS_MESSAGE, 'delete category'),
            );

        } catch (\Exception $e) {
            report($e);

            Log::error(sprintf(MessageEnum::ERROR_EXECPTION, 'deleting category', $e->getMessage()));

            DB::rollBack();

            return $this->errorResponseDto(sprintf(MessageEnum::FAILED_MESSAGE, 'delete category', $e->getMessage()));
        }
    }
}
