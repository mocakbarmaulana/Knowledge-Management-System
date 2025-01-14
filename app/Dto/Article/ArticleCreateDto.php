<?php

namespace App\Dto\Article;

use Spatie\LaravelData\Data;

class ArticleCreateDto extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public string $slug,
        public string $status,
        public int $user_id
    ) {}
}
