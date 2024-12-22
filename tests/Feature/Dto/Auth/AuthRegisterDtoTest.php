<?php

it("test_AuthRegisterDto_success", function() {
    $data = [
        "name" => "John Doe",
        "email" => "john@gmail.com",
        "password" => "password",
    ];

    $dto = new \App\Dto\Auth\AuthRegisterDto(
        name: $data["name"],
        email: $data["email"],
        password: $data["password"],
    );

    expect($dto->name)->toBe($data["name"]);
    expect($dto->email)->toBe($data["email"]);
    expect($dto->password)->toBe($data["password"]);
});
