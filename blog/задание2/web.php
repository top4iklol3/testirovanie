<?php
// routes/api.php

use App\Http\Controllers\PollController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Правильная структура маршрутов
Route::prefix('api')->group(function () {
    // Маршруты для опросов
    Route::prefix('polls')->group(function () {
        Route::get('/', [PollController::class, 'index']);
        Route::post('/', [PollController::class, 'store']);
        Route::get('/{poll}', [PollController::class, 'show']);
        Route::delete('/{poll}', [PollController::class, 'destroy']);

        Route::post('/{poll}/vote', [VoteController::class, 'vote']);
        Route::get('/{poll}/results', [VoteController::class, 'results']);
    });
});
