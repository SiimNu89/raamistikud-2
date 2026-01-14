<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::post('/add-comment/{post}', [CommentController::class, 'store'])->name('comments.add');


});

require __DIR__.'/settings.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';
