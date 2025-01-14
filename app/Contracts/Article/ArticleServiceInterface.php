<?php

namespace App\Contracts\Article;

use App\Dto\Article\ArticleCreateDto;
use App\Dto\Article\ArticleGetDto;
use App\Dto\DefaultResponseDto;

interface ArticleServiceInterface
{
    public function getArticles(ArticleGetDto $payload): DefaultResponseDto;
    public function createArticle(ArticleCreateDto $payload): DefaultResponseDto;
    public function showArticle(int $id): DefaultResponseDto;
    public function updateArticle(int $id, ArticleCreateDto $payload): DefaultResponseDto;
    public function deleteArticle(int $id): DefaultResponseDto;
}
