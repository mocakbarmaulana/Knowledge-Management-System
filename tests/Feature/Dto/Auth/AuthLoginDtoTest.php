<?php

it("test_AuthLoginDto_success", function () {
    $dto = new \App\Dto\Auth\AuthLoginDto(
        email: "test@test.com",
        password: "password"
    );

    expect($dto->email)->toBe("test@test.com");
    expect($dto->password)->toBe("password");
});
