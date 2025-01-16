<?php

namespace App\Dto\Tag;

use Spatie\LaravelData\Data;

class TagGetDto extends Data
{
    public function __construct(
        public int $page,
        public int $perPage,
        public string $search = ''
    ) {
    }
}
