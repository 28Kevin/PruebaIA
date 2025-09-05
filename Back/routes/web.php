<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Weather Chatbot API',
        'version' => '1.0.0',
        'status' => 'active',
        'endpoints' => [
            'health' => '/api/health',
            'conversations' => '/api/conversations',
            'send_message' => '/api/conversations/{id}/messages'
        ]
    ]);
});
