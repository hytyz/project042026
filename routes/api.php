<?php

use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\FlashcardSetController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/flashcards/generate', [FlashcardController::class, 'generate']);

    Route::apiResource('sets', FlashcardSetController::class);

    Route::apiResource('tags', TagController::class);
});
