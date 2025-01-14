<?php

namespace App\Services;

use App\Contracts\Article\ArticleServiceInterface;
use App\Dto\Article\ArticleCreateDto;
use App\Dto\Article\ArticleGetDto;
use App\Dto\DefaultResponseDto;
use App\Models\Article;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleService implements ArticleServiceInterface
{
    use JsonResponseTrait;

    protected $articleNotFound = 'Article not found';

    protected Article $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Get all articles
     *
     * @param ArticleGetDto $payload
     * @return DefaultResponseDto
     */
    public function getArticles(ArticleGetDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $articles = $this->article->query()
                ->where('title', 'like', '%' . $payload->search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate($payload->perPage, page: $payload->page);

            $response = [
                'content' => $articles->items(),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'prev_page' => $articles->currentPage() === 1 ? null : $articles->currentPage() - 1,
                    'next_page' => $articles->perPage() * $articles->currentPage() >= $articles->total() ? null : $articles->currentPage() + 1,
                ]
            ];

            return $this->successResponseDto(
                'Articles fetched successfully',
                $response
            );
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during article fetching: ' . $e->getMessage());

            return $this->errorResponseDto('Failed to fetch articles | ' . $e->getMessage());
        }
    }

    /**
     * Create a new article
     *
     * @param ArticleCreateDto $payload
     * @return DefaultResponseDto
     */
    public function createArticle(ArticleCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $exists = $this->article->where('slug', $payload->slug)->exists();

            if ($exists) {
                return $this->errorResponseDto('Article is already exists with the same title');
            }

            $article = $this->article->create([
                'title' => $payload->title,
                'content' => $payload->content,
                'slug' => $payload->slug,
                'status' => $payload->status,
                'user_id' => $payload->user_id
            ]);

            DB::commit();

            return $this->successResponseDto(
                'Article created successfully',
                $article->toArray(),
            );
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during article creation: ' . $e->getMessage());

            DB::rollBack();

            return $this->errorResponseDto('Failed to create article | ' . $e->getMessage());
        }
    }

    /**
     * Get article detail
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function showArticle(int $id): DefaultResponseDto
    {
        try {
            $article = $this->article->find($id);

            if (!$article) {
                return $this->errorResponseDto($this->articleNotFound);
            }

            return $this->successResponseDto(
                'Article fetched successfully',
                $article->toArray(),
            );
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during detail article fetching: ' . $e->getMessage());

            return $this->errorResponseDto('Failed to fetch detail article | ' . $e->getMessage());
        }
    }

    /**
     * Update an article
     *
     * @param int $id
     * @param ArticleCreateDto $payload
     * @return DefaultResponseDto
     */
    public function updateArticle(int $id, ArticleCreateDto $payload): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $article = $this->validationUpdateArticle($id, $payload);

            if ($article instanceof DefaultResponseDto) {
                return $article;
            }

            $article->update([
                'title' => $payload->title,
                'content' => $payload->content,
                'slug' => $payload->slug,
                'status' => $payload->status,
                'user_id' => $payload->user_id
            ]);

            DB::commit();

            return $this->successResponseDto(
                'Article updated successfully',
                $article->toArray(),
            );
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during article update: ' . $e->getMessage());

            DB::rollBack();

            return $this->errorResponseDto('Failed to update article | ' . $e->getMessage());
        }
    }

    /**
     * Delete an article
     *
     * @param int $id
     * @return DefaultResponseDto
     */
    public function deleteArticle(int $id): DefaultResponseDto
    {
        try {
            DB::beginTransaction();

            $article = $this->article->find($id);

            if (!$article) {
                return $this->errorResponseDto($this->articleNotFound);
            }

            $article->delete();

            DB::commit();

            return $this->successResponseDto('Article deleted successfully');
        } catch (\Exception $e) {
            report($e);

            Log::error('Unexpected error during article deletion: ' . $e->getMessage());

            DB::rollBack();

            return $this->errorResponseDto('Failed to delete article | ' . $e->getMessage());
        }
    }

    /**
     * Validate and get article for update
     *
     * @param int $id
     * @param ArticleCreateDto $payload
     * @return DefaultResponseDto|Article
     */
    private function validationUpdateArticle(int $id, ArticleCreateDto $payload): DefaultResponseDto|Article
    {
        $article = $this->article->find($id);

        if (!$article) {
            return $this->errorResponseDto($this->articleNotFound);
        }

        $exists = $this->article->where('slug', $payload->slug)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return $this->errorResponseDto('Article is already exists with the same title');
        }

        return $article;
    }
}
