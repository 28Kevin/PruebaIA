<?php

namespace App\Services\Contracts;

use App\Models\Conversation;
use App\Models\Message;

interface ChatServiceInterface
{
    public function createConversation(?string $title = null): Conversation;
    
    public function getConversation(int $conversationId): ?Conversation;
    
    public function sendMessage(int $conversationId, string $content): Message;
}
