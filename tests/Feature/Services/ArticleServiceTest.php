<?php

use App\Dto\Article\ArticleCreateDto;
use App\Dto\Article\ArticleGetDto;
use App\Dto\DefaultResponseDto;
use App\Enum\MessageEnum;
use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    /** @var \App\Models\Article|Mockery\MockInterface $mockArticleModel */
    $this->mockArticleModel = Mockery::mock('alias:' . Article::class);
    /** @var \App\Models\Category|Mockery\MockInterface $mockCategoryModel */
    $this->mockCategoryModel = Mockery::mock('alias:' . Category::class);

    $this->articleService = new ArticleService($this->mockArticleModel, $this->mockCategoryModel);
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

        $response = $this->articleService->getArticles($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe(sprintf(MessageEnum::SUCCESS_MESSAGE, 'fetch articles'));
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

        $response = $this->articleService->getArticles($payload);

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
        category: 'Category 1',
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

        $this->mockCategoryModel
            ->shouldReceive('where->first')
            ->once()
            ->andReturnNull();
        $this->mockCategoryModel
            ->shouldReceive('create')
            ->once()
            ->andReturn((object) ['id' => 1]);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $response = $this->articleService->createArticle($payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe(sprintf(MessageEnum::SUCCESS_MESSAGE, 'create article'));
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

        $response = $this->articleService->createArticle($payload);

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

        $this->mockCategoryModel
            ->shouldReceive('where->first')
            ->once()
            ->andReturn((object) ['id' => 1]);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $response = $this->articleService->createArticle($payload);

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

        $response = $this->articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe(sprintf(MessageEnum::SUCCESS_MESSAGE, 'show article'));
        expect($response->data)->toHaveKey('id');
        expect($response->data)->toHaveKey('title');
        expect($response->data)->toHaveKey('content');
    });

    it('should return not found response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->once()
            ->andReturnNull();

        $response = $this->articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $response = $this->articleService->showArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe(sprintf(MessageEnum::FAILED_MESSAGE, 'show article', 'An error occurred'));
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
        category: 'Category 1',
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

        $this->mockCategoryModel
            ->shouldReceive('where->first')
            ->once()
            ->andReturnNull();
        $this->mockCategoryModel
            ->shouldReceive('create')
            ->once()
            ->andReturn((object) ['id' => 1]);

        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $response = $this->articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(true);
        expect($response->message)->toBe(sprintf(MessageEnum::SUCCESS_MESSAGE, 'update article'));
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

        $response = $this->articleService->updateArticle($id, $payload);

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

        $response = $this->articleService->updateArticle($id, $payload);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id, $payload) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $response = $this->articleService->updateArticle($id, $payload);

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

        $response = $this->articleService->deleteArticle($id);

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

        $response = $this->articleService->deleteArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Article not found');
        expect($response->data)->toBeNull();
    });

    it('should return error response', function () use ($id) {
        $this->mockArticleModel
            ->shouldReceive('find')
            ->andThrow(new \Exception('An error occurred'));

        $response = $this->articleService->deleteArticle($id);

        expect($response)->toBeInstanceOf(DefaultResponseDto::class);
        expect($response->status)->toBe(false);
        expect($response->message)->toBe('Failed to delete article | An error occurred');
        expect($response->data)->toBeNull();
    });
});
