<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WeatherController;
use App\Mail\Timetable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Inertia\Inertia;

Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [WeatherController::class, 'index'])->name('dashboard');

    Route::post('/markers', [MarkerController::class, 'store'])->name('markers.store');
    Route::put('/markers/{marker}', [MarkerController::class, 'update'])->name('markers.update');
    Route::delete('/markers/{marker}', [MarkerController::class, 'destroy'])->name('markers.destroy');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');

    Route::post('/add-comment/{post}', [CommentController::class, 'store'])->name('comments.add');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/mailable', function () {
    $startDate = now()->startOfWeek();
    $endDate   = now()->endOfWeek();

    $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
        'from' => $startDate->toIsoString(),
        'thru' => $endDate->toIsoString(),
        'lang' => 'ET',
        'page' => 0,
        'schoolId' => 38,
        'size' => 50,
        'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
    ]);

    $timetableEvents = collect($response['content'] ?? [])
        ->sortBy(fn($event) => $event['date'] . ' ' . $event['timeStart'])
        ->groupBy(fn($event) =>
            Carbon::parse($event['date'])
                ->locale('et_EE')
                ->dayName
        );

    return new Timetable($timetableEvents, $startDate, $endDate);
});

require __DIR__.'/settings.php';
require __DIR__.'/posts.php';
require __DIR__.'/authors.php';
