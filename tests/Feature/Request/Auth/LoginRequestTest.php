<?php

describe("LoginRequest", function () {
    it("should authorize the request", function () {
        $request = new \App\Http\Requests\Auth\LoginRequest();
        expect($request->authorize())->toBeTrue();
    });

    it("should return the validation rules", function () {
        $request = new \App\Http\Requests\Auth\LoginRequest();
        expect($request->rules())->toBe([
            "email" => ["required", "email"],
            "password" => ["required", "string"],
        ]);
    });
});
