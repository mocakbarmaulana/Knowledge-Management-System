<?php

namespace App\Dto\Tag;

use Spatie\LaravelData\Data;

class TagCreateDto extends Data
{
    public function __construct(
        public string $name,
        public string $description
    ) {}
}
