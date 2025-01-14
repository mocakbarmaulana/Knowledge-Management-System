<?php

describe('PaginationRequest_authorize', function () {
    it('should return false', function () {
        $paginationRequest = new \App\Http\Requests\PaginationRequest();
        expect($paginationRequest->authorize())->toBeFalse();
    });
});


describe('PaginationRequest_rules', function() {
    it('should return an validation array', function() {
        $paginationRequest = new \App\Http\Requests\PaginationRequest();

        expect($paginationRequest->rules())->toBeArray();
        expect($paginationRequest->rules())->toBe([
            'page' => 'required|integer',
            'per_page' => 'required|integer',
        ]);
    });
});
