<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home', function () {
    return view('home');
});
Route::post('/random-word', [APIController::class, 'generateRandomWord'])->name('generateRandomWord');
Route::post('/translate/{language}/{word}', [APIController::class, 'translate'])->name('translate');
Route::post('/example-sentences/{word}', [APIController::class, 'exampleSentences'])->name('exampleSentences');
Route::post('/speech-to-text', [APIController::class, 'speechToText'])->name('speechToText');
Route::post('/text-to-speech/{word}', [APIController::class, 'textToSpeech'])->name('textToSpeech');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
