<?php

use App\Http\Controllers\Api\v1\PropertySearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/v1')->group(static function () {
    Route::prefix('/properties')->group(static function () {
        Route::get('/search', [PropertySearchController::class, 'search']);
    });
});
