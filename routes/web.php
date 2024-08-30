<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LatihanController;

Route::get('/', function () {
    if (auth()->check()) {
        return app(LatihanController::class)->beranda();
    } else {
        return app(ProfileController::class)->welcome();
    }
})->name('beranda');
Route::get('/captcha/refresh', function () {
    return response(captcha_src())->header('Content-Type', 'image/png');
});


Route::middleware('auth')->group(function () {
    Route::get('/panduan', [LatihanController::class, 'panduan'])->name('panduan');
    Route::middleware('verified')->group(function () {
        Route::resource('latihan', LatihanController::class)->only(['index', 'store', 'show']);
        Route::get('/latihan/{latihan}/{jenisLatihan}', [LatihanController::class, 'latihan'])->name('latihan');
        Route::patch('/latihan/{latihan}/{jenisLatihan}', [LatihanController::class, 'update'])->name('latihan.update');
        Route::get('/riwayat', [LatihanController::class, 'riwayat'])->name('riwayat');
        Route::get('/detail-riwayat/{id}', [LatihanController::class, 'detailRiwayat'])->name('detailRiwayat');
        Route::get('/latihan/{latihan}/artikata/soal', [LatihanController::class, 'soalArtikata'])->name('soalArtikata');

        // API Route
        Route::post('/word/{language}/{category}', [APIController::class, 'getWord'])->name('getWord');
        Route::post('/example-sentences', [APIController::class, 'exampleSentences'])->name('exampleSentences');
        Route::post('/speech-to-text', [APIController::class, 'speechToText'])->name('speechToText');
        Route::post('/text-to-speech', [APIController::class, 'textToSpeech'])->name('textToSpeech');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
