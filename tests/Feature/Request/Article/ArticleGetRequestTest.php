<?php

describe('ArticleGetRequest_authorize', function () {
    it('should return true', function () {
        $articleGetRequest = new \App\Http\Requests\Article\ArticleGetRequest();
        expect($articleGetRequest->authorize())->toBeTrue();
    });
});

describe('ArticleGetRequest_rules', function() {
    it('should return an validation array', function() {
        $articleGetRequest = new \App\Http\Requests\Article\ArticleGetRequest();

        expect($articleGetRequest->rules())->toBeArray();
        expect($articleGetRequest->rules())->toBe([
            'page' => 'required|integer',
            'per_page' => 'required|integer',
            'search' => 'nullable|string',
        ]);
    });
});
