<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthorController;

// Posts routes
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

// Homepage & dashboard
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authors routes (full CRUD)
Route::resource('authors', AuthorController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

/* existing includes */
require __DIR__.'/settings.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';
