<?php

use App\Http\Controllers\ConversationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Conversation routes - NO AUTHENTICATION required for development
Route::prefix('conversations')->group(function () {
    Route::post('/', [ConversationController::class, 'store']);
    Route::get('/{conversation}', [ConversationController::class, 'show']);
    Route::post('/{conversation}/messages', [ConversationController::class, 'sendMessage']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Weather Chatbot API'
    ]);
});

// Handle preflight OPTIONS requests
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

# cGFuZ29saW4=
