<?php

describe('ArticleCreateRequest_authorize', function () {
    it('should return true', function () {
        $articleCreateRequest = new \App\Http\Requests\Article\ArticleCreateRequest();
        expect($articleCreateRequest->authorize())->toBeTrue();
    });
});

describe('ArticleCreateRequest_rules', function() {
    it('should return an validation array', function() {
        $articleCreateRequest = new \App\Http\Requests\Article\ArticleCreateRequest();

        expect($articleCreateRequest->rules())->toBeArray();
        expect($articleCreateRequest->rules())->toBe([
            'title' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'category' => 'nullable|string'
        ]);
    });
});

describe('ArticleCreateRequest_passedValidation', function() {
    it('should return an validation array', function() {
        $articleCreateRequest = new \App\Http\Requests\Article\ArticleCreateRequest();
        $articleCreateRequest->merge([
            'title' => 'Hello World',
            'content' => 'Hello World Content',
            'status' => 'draft',
        ]);

        $articleCreateRequest->passedValidation();

        expect($articleCreateRequest->slug)->toBe('hello-world');
        expect($articleCreateRequest->user_id)->toBeNull();
    });
});
