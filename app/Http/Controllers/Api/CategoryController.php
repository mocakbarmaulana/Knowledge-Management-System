<?php

namespace App\Http\Controllers\Api;

use App\Dto\Category\CategoryCreateDto;
use App\Dto\Category\CategoryGetDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryGetRequest;
use App\Services\CategoryService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class CategoryController
 *
 * @group Category
 *
 * APIs for managing categories
 *
 * @authenticated
 * @package App\Http\Controllers\Api
 */
class CategoryController extends Controller
{
    use JsonResponseTrait;

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Get a list of categories
     *
     * @param CategoryGetRequest $request
     * @return JsonResponse
     */
    public function index(CategoryGetRequest $request): JsonResponse
    {
        $payload = new CategoryGetDto(
            page: $request->input('page', 1),
            perPage: $request->input('per_page', 100),
            search: $request->input('search', ''),
        );

        $result = $this->categoryService->getCategories($payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Create a new category
     *
     * @param CategoryCreateRequest $request
     * @return JsonResponse
     */
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        $payload = new CategoryCreateDto(
            name: Str::lower($request->input('name')),
            description: $request->input('description'),
        );

        $result = $this->categoryService->createCategory($payload);

        return $this->buildResponse($result, $result->status ? 201 : 400);
    }

    /**
     * Get a single category
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->categoryService->showCategory($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Update a category
     *
     * @param CategoryCreateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CategoryCreateRequest $request, int $id): JsonResponse
    {
        $payload = new CategoryCreateDto(
            name: Str::lower($request->input('name')),
            description: $request->input('description'),
        );

        $result = $this->categoryService->updateCategory($id, $payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Delete a category
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $result = $this->categoryService->deleteCategory($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

}
