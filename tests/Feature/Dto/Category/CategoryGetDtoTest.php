<?php

describe('CategoryGetDto_success', function () {
    it('should return the correct data', function () {
        $dto = new \App\Dto\Category\CategoryGetDto(
            page: 1,
            perPage: 10,
            search: 'test'
        );

        expect($dto->page)->toBe(1);
        expect($dto->perPage)->toBe(10);
        expect($dto->search)->toBe('test');
    });
});
