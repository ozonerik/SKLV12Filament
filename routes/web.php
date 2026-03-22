<?php

use App\Http\Controllers\LulusanImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('signed')->group(function () {
    Route::get('/imports/lulusan/{token}/confirm', [LulusanImportController::class, 'confirm'])
        ->name('lulusan-import.confirm');
    Route::get('/imports/lulusan/{token}/cancel', [LulusanImportController::class, 'cancel'])
        ->name('lulusan-import.cancel');
});
