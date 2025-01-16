<?php

namespace App\Http\Controllers\Api;

use App\Dto\Tag\TagCreateDto;
use App\Dto\Tag\TagGetDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\TagCreateRequest;
use App\Http\Requests\Tag\TagGetRequest;
use App\Services\TagService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    use JsonResponseTrait;

    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Get a list of tags
     *
     * @param TagGetRequest $request
     * @return JsonResponse
     */
    public function index(TagGetRequest $request): JsonResponse
    {
        $payload = new TagGetDto(
            page: $request->input('page', 1),
            perPage: $request->input('per_page', 10),
            search: $request->input('search', ''),
        );

        $result = $this->tagService->getTags($payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Create a new tag
     *
     * @param TagCreateRequest $request
     * @return JsonResponse
     */
    public function create(TagCreateRequest $request): JsonResponse
    {
        $payload = new TagCreateDto(
            name: $request->input('name'),
            description: $request->input('description'),
        );

        $result = $this->tagService->createTag($payload);

        return $this->buildResponse($result, $result->status ? 201 : 400);
    }

    /**
     * Show a tag
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->tagService->showTag($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Update a tag
     *
     * @param TagCreateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(TagCreateRequest $request, int $id): JsonResponse
    {
        $payload = new TagCreateDto(
            name: $request->input('name'),
            description: $request->input('description'),
        );

        $result = $this->tagService->updateTag($id, $payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Delete a tag
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->tagService->deleteTag($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }
}
