<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::resource('posts', PostController::class)->middleware(['auth', 'verified']);
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
