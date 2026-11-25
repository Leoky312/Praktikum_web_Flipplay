<?php

use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ViewerController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters([
    'media' => 'media',
]);

Route::apiResource('viewers', ViewerController::class)->only(['index', 'store', 'show']);

Route::get('/viewers/{viewer}/watchlist', [ViewerController::class, 'watchlist'])
    ->name('viewers.watchlist.index');

Route::post('/viewers/{viewer}/watchlist', [ViewerController::class, 'addToWatchlist'])
    ->name('viewers.watchlist.attach');

Route::delete('/viewers/{viewer}/watchlist/{media}', [ViewerController::class, 'removeFromWatchlist'])
    ->name('viewers.watchlist.detach');
