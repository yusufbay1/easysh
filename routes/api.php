<?php

use App\Http\Controllers\GetController;
use App\Http\Controllers\PostController;
use Router\Route;

Route::post('/products/([0-9]*)', [PostController::class, 'index']);

Route::prefix('/prefix', function () {
    Route::get('/sample', [GetController::class, 'index']);
});