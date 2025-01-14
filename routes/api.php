<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix("/v1")->group(function () {
    Route::group(["prefix" => "/auth"], function () {
        Route::post("/register", [\App\Http\Controllers\Api\AuthController::class, "register"]);
        Route::post("/login", [\App\Http\Controllers\Api\AuthController::class, "login"]);
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::group(["prefix" => "/articles"], function () {
            $id = "/{id}";
            Route::get('/', [\App\Http\Controllers\Api\ArticleController::class, "index"]);
            Route::post('/', [\App\Http\Controllers\Api\ArticleController::class, "create"]);
            Route::get($id, [\App\Http\Controllers\Api\ArticleController::class, "show"]);
            Route::put($id, [\App\Http\Controllers\Api\ArticleController::class, "update"]);
            Route::delete($id, [\App\Http\Controllers\Api\ArticleController::class, "delete"]);
        });

        Route::post("/auth/logout", [\App\Http\Controllers\Api\AuthController::class, "logout"]);
    });
});
