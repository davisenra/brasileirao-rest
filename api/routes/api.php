<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\StadiumController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/access_token', [AuthController::class, 'accessToken']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/stadiums', [StadiumController::class, 'index']);
    Route::get('/v1/stadiums/{id}', [StadiumController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/v1/clubs', [ClubController::class, 'index']);
    Route::get('/v1/clubs/{id}', [ClubController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/v1/seasons', [SeasonController::class, 'index']);
    Route::get('/v1/seasons/{id}', [SeasonController::class, 'show'])->where('id', '[0-9]+');
});
