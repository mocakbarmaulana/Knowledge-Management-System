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

        Route::group(["prefix" => "/categories"], function () {
            $id = "/{id}";
            Route::get('/', [\App\Http\Controllers\Api\CategoryController::class, "index"]);
            Route::post('/', [\App\Http\Controllers\Api\CategoryController::class, "create"]);
            Route::get($id, [\App\Http\Controllers\Api\CategoryController::class, "show"]);
            Route::put($id, [\App\Http\Controllers\Api\CategoryController::class, "update"]);
            Route::delete($id, [\App\Http\Controllers\Api\CategoryController::class, "delete"]);
        });

        Route::group(["prefix" => "/tags"], function () {
            $id = "/{id}";
            Route::get('/', [\App\Http\Controllers\Api\TagController::class, "index"]);
            Route::post('/', [\App\Http\Controllers\Api\TagController::class, "create"]);
            Route::get($id, [\App\Http\Controllers\Api\TagController::class, "show"]);
            Route::put($id, [\App\Http\Controllers\Api\TagController::class, "update"]);
            Route::delete($id, [\App\Http\Controllers\Api\TagController::class, "delete"]);
        });

        Route::post("/auth/logout", [\App\Http\Controllers\Api\AuthController::class, "logout"]);
    });
});
