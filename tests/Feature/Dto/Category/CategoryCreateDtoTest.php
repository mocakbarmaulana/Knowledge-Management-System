<?php

describe('CategoryCreateDtoTest_success', function() {
    it('should return the correct data', function() {
        $dto = new \App\Dto\Category\CategoryCreateDto(
            name: 'test',
            description: 'test'
        );

        expect($dto->name)->toBe('test');
        expect($dto->description)->toBe('test');
    });
});
