<?php

namespace App\Dto\Category;

use Spatie\LaravelData\Data;

class CategoryGetDto extends Data
{
    public function __construct(
        public int $page,
        public int $perPage,
        public string $search = ''
    ) {}
}
