<?php

namespace App\Services\Contracts;

use App\DTOs\OpenAIResponseDTO;

interface OpenAIServiceInterface
{
    public function sendMessage(string $message, array $conversationHistory = []): OpenAIResponseDTO;
    
    public function sendMessageWithToolResult(string $originalMessage, array $toolResult, array $conversationHistory = []): OpenAIResponseDTO;
}
