<?php

namespace App\Http\Controllers\Api;

use App\Dto\Article\ArticleCreateDto;
use App\Dto\Article\ArticleGetDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ArticleCreateRequest;
use App\Http\Requests\Article\ArticleGetRequest;
use App\Services\ArticleService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleController
 *
 * @group Article
 *
 * APIs for managing articles
 *
 * @authenticated
 * @package App\Http\Controllers\Api
 */
class ArticleController extends Controller
{
    use JsonResponseTrait;

    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Get a list of articles
     *
     * @param ArticleGetRequest $request
     * @return JsonResponse
     */
    public function index(ArticleGetRequest $request): JsonResponse
    {
        $payload = new ArticleGetDto(
            page: $request->input('page', 1),
            perPage: $request->input('per_page', 10),
            search: $request->input('search', ''),
        );

        $result = $this->articleService->getArticles($payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Create a new article
     *
     * @param ArticleCreateRequest $request
     * @return JsonResponse
     */
    public function create(ArticleCreateRequest $request): JsonResponse
    {
        $payload = new ArticleCreateDto(
            title: $request->title,
            content: $request->content,
            slug: $request->slug,
            status: $request->status,
            user_id: $request->user_id,
        );

        $result = $this->articleService->createArticle($payload);

        return $this->buildResponse($result, $result->status ? 201 : 400);
    }

    /**
     * Get a single article
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->articleService->showArticle($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Update an article
     *
     * @param ArticleCreateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ArticleCreateRequest $request, int $id): JsonResponse
    {
        $payload = new ArticleCreateDto(
            title: $request->title,
            content: $request->content,
            slug: $request->slug,
            status: $request->status,
            user_id: $request->user_id,
        );

        $result = $this->articleService->updateArticle($id, $payload);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }

    /**
     * Delete an article
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $result = $this->articleService->deleteArticle($id);

        return $this->buildResponse($result, $result->status ? 200 : 400);
    }
}
