<?php

it("test_DefaultResponseDto_success", function() {
    $dto = new \App\Dto\DefaultResponseDto(
        status: true,
        message: "Success",
        data: ["key" => "value"]
    );

    expect($dto->status)->toBe(true);
    expect($dto->message)->toBe("Success");
    expect($dto->data)->toBe(["key" => "value"]);
});
