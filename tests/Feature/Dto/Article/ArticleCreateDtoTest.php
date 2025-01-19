<?php

describe('ArticleCreateDto', function () {
    it('should have the correct properties', function () {
        $dto = new \App\Dto\Article\ArticleCreateDto(
            title: 'Title',
            content: 'Content',
            slug: 'slug',
            status: 'published',
            user_id: 1,
            category: 'category',
        );

        expect($dto->title)->toBe('Title');
        expect($dto->content)->toBe('Content');
        expect($dto->slug)->toBe('slug');
        expect($dto->status)->toBe('published');
        expect($dto->user_id)->toBe(1);
        expect($dto->category)->toBe('category');
    });
});
