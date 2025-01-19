<?php

namespace App\Dto\Article;

use Spatie\LaravelData\Data;

class ArticleGetDto extends Data
{
    public function __construct(
        public int $page,
        public int $perPage,
        public ?string $search = ''
    ) {}
}
