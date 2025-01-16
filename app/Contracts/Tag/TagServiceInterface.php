<?php

namespace App\Contracts\Tag;

use App\Dto\DefaultResponseDto;
use App\Dto\Tag\TagCreateDto;
use App\Dto\Tag\TagGetDto;

interface TagServiceInterface
{
    public function getTags(TagGetDto $payload): DefaultResponseDto;

    public function createTag(TagCreateDto $payload): DefaultResponseDto;

    public function showTag(int $id): DefaultResponseDto;

    public function updateTag(int $id, TagCreateDto $payload): DefaultResponseDto;

    public function deleteTag(int $id): DefaultResponseDto;
}
