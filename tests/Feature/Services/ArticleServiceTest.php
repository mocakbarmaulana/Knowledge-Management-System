<?php

use App\Dto\Article\ArticleCreateDto;
use App\Dto\Article\ArticleGetDto;
use App\Dto\DefaultResponseDto;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    /** @var \App\Models\Article|Mockery\MockInterface $mockArticleModel */
    $this->mockArticleModel = Mockery::mock('alias:' . Article::class);
});

describe('ArticleService_getArticles', function () {
    it('should return success response', function () {
        $payload = new ArticleGetDto(
            page: 1,
            perPage: 10,
            search: '',
        );

        $this->mockArticleModel
            ->shouldReceive('query->where->orderBy->paginate')
            ->once()
            ->andReturn(new LengthAwarePaginator([
                [
                    'id' => 1,
                    'title' => 'Article 1',
                    'content' => 'Content 1',
                ],
                [
                    'id' => 2,
                    'title' => 'Article 2',
                    'content' => 'Content 2',
                ]
            ], 2, 10, 1));

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->getArticles($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe('Articles fetched successfully');
        expect($response->data)->toHaveKey('content');
        expect($response->data)->toHaveKey('pagination');
    });

    it('should return error response', function () {
        $payload = new ArticleGetDto(
            page: 1,
            perPage: 10,
            search: '',
        );

        $this->mockArticleModel
            ->shouldReceive('query->where->orderBy->paginate')
            ->andThrow(new \Exception('An error occurred'));

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->getArticles($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to fetch articles | An error occurred');
        expect($response->data)->toBeNull();
    });
});

describe('ArticleService_createArticle', function () {
    $payload = new ArticleCreateDto(
        title: 'Article 1',
        content: 'Content 1',
        slug: 'article-1',
        status: 'published',
        user_id: 1,
    );

    it('should return success response', function () use ($payload) {
        $this->mockArticleModel
            ->shouldReceive('where->exists')
            ->once()
            ->andReturn(false);

        $this->mockArticleModel
            ->shouldReceive('create')
            ->once()
            ->andReturn(new class {
                public $id = 1;
                public $title = 'Article 1';
                public $content = 'Content 1';

                public function toArray()
                {
                    return [
                        'id' => $this->id,
                        'title' => $this->title,
                        'content' => $this->content,
                    ];
                }
            });

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->createArticle($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe('Article created successfully');
        expect($response->data)->toHaveKey('id');
        expect($response->data)->toHaveKey('title');
        expect($response->data)->toHaveKey('content');
    });

    it('should return article already exists', function () use ($payload) {
        $this->mockArticleModel
            ->shouldReceive('where->exists')
            ->once()
            ->andReturn(true);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->createArticle($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article is already exists with the same title');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($payload) {
        $this->mockArticleModel
            ->shouldReceive('where->exists')
            ->once()
            ->andReturn(false);

        $this->mockArticleModel
            ->shouldReceive('create')
            ->andThrow(new \Exception('An error occurred'));

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->createArticle($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to create article | An error occurred');
        expect($response->data)->toBeNull();
    });
});

describe('ArticleService_showArticle', function() {
    $id = 10;

    it('should return success response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturn(new class {
                public $id = 10;
                public $title = 'Article 10';
                public $content = 'Content 10';

                public function toArray()
                {
                    return [
                        'id' => $this->id,
                        'title' => $this->title,
                        'content' => $this->content,
                    ];
                }
            });

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe('Article fetched successfully');
        expect($response->data)->toHaveKey('id');
        expect($response->data)->toHaveKey('title');
        expect($response->data)->toHaveKey('content');
    });

    it('should return not found response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to fetch detail article | An error occurred');
        expect($response->data)->toBeNull();
    });
});

describe('ArticleService_updateArticle', function() {
    $payload = new ArticleCreateDto(
        title: 'Article 23 Updated',
        content: 'Content 23 Updated',
        slug: 'article-23-updated',
        status: 'published',
        user_id: 1,
    );

    $id = 23;

    it('should return success response', function () use ($id, $payload) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnUsing(function () {
                $mockArticle = Mockery::mock('alias:' . Article::class)
                    ->makePartial();
                $mockArticle->shouldReceive('update')
                    ->once()
                    ->andReturnSelf();
                $mockArticle->shouldReceive('toArray')
                    ->once()
                    ->andReturn([
                        'id' => 23,
                        'title' => 'Article 23 Updated',
                        'content' => 'Content 23 Updated',
                    ]);
                return $mockArticle;
            });

        $this->mockArticleModel
            ->shouldReceive('where->where->exists')
            ->once()
            ->andReturn(false);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe('Article updated successfully');
        expect($response->data)->toHaveKey('id');
        expect($response->data)->toHaveKey('title');
        expect($response->data)->toHaveKey('content');
    });

    it('should return article already exists', function () use ($id, $payload) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnUsing(function () {
                $mockArticle = Mockery::mock('alias:' . Article::class)
                    ->makePartial();
                $mockArticle->shouldReceive('update')
                    ->never();
                return $mockArticle;
            });

        $this->mockArticleModel
            ->shouldReceive('where->where->exists')
            ->once()
            ->andReturn(true);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article is already exists with the same title');
        expect($response->data)->toBeNull();
    });

    it('should return not found response', function () use ($id, $payload) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id, $payload) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to update article | An error occurred');
        expect($response->data)->toBeNull();
    });
});

describe('ArticleService_deleteArticle', function() {
    $id = 77;

    it('should return success response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturn(new class {
                public $id = 77;

                public function delete()
                {
                    return true;
                }
            });

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->deleteArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe('Article deleted successfully');
        expect($response->data)->toBe([]);
    });

    it('should return not found response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnNull();

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->deleteArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $articleService = new ArticleService($this->mockArticleModel);
        $response = $articleService->deleteArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to delete article | An error occurred');
        expect($response->data)->toBeNull();
    });
});
