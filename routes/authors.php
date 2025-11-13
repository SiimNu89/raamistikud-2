<?php

use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('authors', AuthorController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
});
