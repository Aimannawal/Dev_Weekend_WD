<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StuntingPredictionController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('stunting')->name('stunting.')->group(function () {
    Route::get('/',         [StuntingPredictionController::class, 'index'])->name('index');
    Route::get('/create',   [StuntingPredictionController::class, 'create'])->name('create');
    Route::post('/',        [StuntingPredictionController::class, 'store'])->name('store');
    Route::get('/{stunting}', [StuntingPredictionController::class, 'show'])->name('show');
});
