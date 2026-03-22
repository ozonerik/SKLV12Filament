<?php

use App\Http\Controllers\LulusanImportController;
use App\Http\Controllers\PublicSklVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSklVerificationController::class, 'landing'])->name('home');
Route::get('/validasi-skl', [PublicSklVerificationController::class, 'home'])->name('skl.verify.search');
Route::get('/validasi-skl/{code}', [PublicSklVerificationController::class, 'show'])->name('skl.verify.show');
Route::get('/validasi-skl/{code}/download', [PublicSklVerificationController::class, 'download'])->name('skl.verify.download');

Route::middleware('signed')->group(function () {
    Route::get('/imports/lulusan/{token}/preview', [LulusanImportController::class, 'preview'])
        ->name('lulusan-import.preview');

    Route::get('/imports/lulusan/{token}/confirm', [LulusanImportController::class, 'confirm'])
        ->name('lulusan-import.confirm');
    Route::get('/imports/lulusan/{token}/cancel', [LulusanImportController::class, 'cancel'])
        ->name('lulusan-import.cancel');
});
