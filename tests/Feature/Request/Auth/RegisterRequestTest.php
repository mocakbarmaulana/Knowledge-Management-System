<?php

describe("RegisterRequest", function() {
    it("should pass the authorization", function() {
        $request = new \App\Http\Requests\Auth\RegisterRequest();
        expect($request->authorize())->toBe(true);
    });

    it("should return the correct validation rules", function() {
        $request = new \App\Http\Requests\Auth\RegisterRequest();

        expect($request->rules())->toBe([
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:users"],
            "password" => ["required", "string", "min:8", "confirmed:password_confirmation"],
            "password_confirmation" => ["required", "string"],
        ]);
    });
});
