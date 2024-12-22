<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("/v1/auth")->group(function () {
    Route::post("/register", [\App\Http\Controllers\Api\AuthController::class, "register"]);
    Route::post("/login", [\App\Http\Controllers\Api\AuthController::class, "login"]);
});
