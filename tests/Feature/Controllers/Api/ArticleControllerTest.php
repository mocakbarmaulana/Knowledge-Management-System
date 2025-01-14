<?php

use App\Dto\DefaultResponseDto;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Requests\Article\ArticleCreateRequest;
use App\Http\Requests\Article\ArticleGetRequest;
use App\Services\ArticleService;

describe('ArticleController_index', function() {
    it('should return 200', function() {
        $request = new ArticleGetRequest([
            'page' => 1,
            'per_page' => 10,
            'search' => '',
        ]);

        /** @var ArticleService|Mockery\MockInterface $mockArticleService */
        $mockArticleService = Mockery::mock(ArticleService::class);
        $mockArticleService->shouldReceive('getArticles')->once()->andReturn(DefaultResponseDto::from([
            'status' => true,
            'message' => 'Success',
            'data' => [],
        ]));

        $articleControler = new ArticleController($mockArticleService);

        $response = $articleControler->index($request);

        expect($response)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($response->getStatusCode())->toBe(200);
        expect($response->getData(true))->toBe([
            'status' => true,
            'message' => 'Success',
            'data' => [],
        ]);
    });
});

describe('ArticleController_create', function() {
    it('should return 200', function() {
        $request = new ArticleCreateRequest([
            'title' => 'Title',
            'slug' => 'slug',
            'content' => 'Content',
            'status' => 'draft',
            'user_id' => 1,
        ]);

        /** @var ArticleService|Mockery\MockInterface $mockArticleService */
        $mockArticleService = Mockery::mock(ArticleService::class);
        $mockArticleService->shouldReceive('createArticle')->once()->andReturn(DefaultResponseDto::from([
            'status' => true,
            'message' => 'Article created successfully',
            'data' => [],
        ]));

        $articleControler = new ArticleController($mockArticleService);

        $response = $articleControler->create($request);

        expect($response)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($response->getStatusCode())->toBe(201);
        expect($response->getData(true))->toBe([
            'status' => true,
            'message' => 'Article created successfully',
            'data' => [],
        ]);
    });
});

describe('ArticleController_show', function() {
    it('should return 200', function() {
        $id = 1;

        /** @var ArticleService|Mockery\MockInterface $mockArticleService */
        $mockArticleService = Mockery::mock(ArticleService::class);
        $mockArticleService->shouldReceive('showArticle')->once()->andReturn(DefaultResponseDto::from([
            'status' => true,
            'message' => 'Article fetched successfully',
            'data' => [],
        ]));

        $articleControler = new ArticleController($mockArticleService);

        $response = $articleControler->show($id);

        expect($response)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($response->getStatusCode())->toBe(200);
        expect($response->getData(true))->toBe([
            'status' => true,
            'message' => 'Article fetched successfully',
            'data' => [],
        ]);
    });
});

describe('ArticleController_update', function() {
    it('should return 200', function() {
        $id = 1;
        $request = new ArticleCreateRequest([
            'title' => 'Title Update',
            'slug' => 'title-update',
            'content' => 'Content Update',
            'status' => 'published',
            'user_id' => 1,
        ]);

        /** @var ArticleService|Mockery\MockInterface $mockArticleService */
        $mockArticleService = Mockery::mock(ArticleService::class);
        $mockArticleService->shouldReceive('updateArticle')->once()->andReturn(DefaultResponseDto::from([
            'status' => true,
            'message' => 'Article updated successfully',
            'data' => [],
        ]));

        $articleControler = new ArticleController($mockArticleService);

        $response = $articleControler->update($request, $id);

        expect($response)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($response->getStatusCode())->toBe(200);
        expect($response->getData(true))->toBe([
            'status' => true,
            'message' => 'Article updated successfully',
            'data' => [],
        ]);
    });
});

describe('ArticleController_delete', function() {
    it('should return 200', function() {
        $id = 1;

        /** @var ArticleService|Mockery\MockInterface $mockArticleService */
        $mockArticleService = Mockery::mock(ArticleService::class);
        $mockArticleService->shouldReceive('deleteArticle')->once()->andReturn(DefaultResponseDto::from([
            'status' => true,
            'message' => 'Article deleted successfully',
            'data' => [],
        ]));

        $articleControler = new ArticleController($mockArticleService);

        $response = $articleControler->delete($id);

        expect($response)->toBeInstanceOf(\Illuminate\Http\JsonResponse::class);
        expect($response->getStatusCode())->toBe(200);
        expect($response->getData(true))->toBe([
            'status' => true,
            'message' => 'Article deleted successfully',
            'data' => [],
        ]);
    });
});
