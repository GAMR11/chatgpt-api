<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GeminiController;

Route::prefix('gemini')->group(function () {
    Route::post('/chat', [GeminiController::class, 'chat']);
    Route::get('/health', [GeminiController::class, 'healthCheck']);
});