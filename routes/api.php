<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GeminiController;

Route::post('/gemini/chat', [GeminiController::class, 'chat']);
