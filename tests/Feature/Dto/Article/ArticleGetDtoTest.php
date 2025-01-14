<?php

describe('ArticleGetDto', function () {
    it('should have the correct properties', function () {
        $dto = new \App\Dto\Article\ArticleGetDto(
            page: 1,
            perPage: 10,
            search: 'search'
        );

        expect($dto->page)->toBe(1);
        expect($dto->perPage)->toBe(10);
        expect($dto->search)->toBe('search');
    });
});
