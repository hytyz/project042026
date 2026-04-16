<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SetController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    // flashcard routes
    Route::inertia('flashcards/review', 'flashcards/review')->name('flashcards.review');

    // sets routes
    Route::inertia('sets', 'sets/index')->name('sets.index');
    Route::get('sets/{set}', [SetController::class, 'show'])->name('sets.show');
    Route::get('sets/{set}/practice', [SetController::class, 'practice'])->name('sets.practice');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}', [AdminController::class, 'userDetails'])->name('users.show');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/sets', [AdminController::class, 'sets'])->name('sets');
    Route::get('/sets/{set}', [AdminController::class, 'setDetails'])->name('sets.show');
    Route::delete('/sets/{set}', [AdminController::class, 'deleteSet'])->name('sets.destroy');
});

require __DIR__.'/settings.php';
