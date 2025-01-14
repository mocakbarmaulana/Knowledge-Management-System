<?php

namespace App\Dto\Article;

use Spatie\LaravelData\Data;

class ArticleDataDto extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public string $slug,
        public string $status,
        public int $user_id,
        public string $created_at,
        public string $updated_at
    ) {}
}
