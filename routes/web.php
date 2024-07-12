<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\LatihanController;

Route::get('/', function () {
    if (auth()->check()) {
        return view('beranda');
    } else {
        return view('welcome');
    }
})->name('beranda');

Route::middleware('auth')->group(function () {
    Route::middleware('verified')->group(function () {
        // Latihan Route
        Route::resource('latihan', LatihanController::class)->only(['index', 'store', 'show', 'edit', 'update']);
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/panduan', function () {
        return view('panduan');
    })->name('panduan');
    Route::get('/riwayat', function () {
        return view('riwayat');
    })->name('riwayat');

    // API Route
    Route::post('/post', [APIController::class, 'generateRandomWord'])->name('generateRandomWord');
    Route::post('/word/{language}/{category}', [APIController::class, 'getWord']);
    Route::post('/translate/{language_code}/{word}', [APIController::class, 'translate'])->name('translate');
    Route::post('/example-sentences/{language}/{word}', [APIController::class, 'exampleSentences'])->name('exampleSentences');
    Route::post('/speech-to-text/', [APIController::class, 'speechToText'])->name('speechToText');
    Route::post('/text-to-speech/{language_code}/{word}', [APIController::class, 'textToSpeech'])->name('textToSpeech');
});

require __DIR__ . '/auth.php';
