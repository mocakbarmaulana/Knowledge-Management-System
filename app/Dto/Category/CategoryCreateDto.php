<?php

namespace App\Dto\Category;

use Spatie\LaravelData\Data;

class CategoryCreateDto extends Data
{
    public function __construct(
        public string $name,
        public ?string $description
    ) {}
}
