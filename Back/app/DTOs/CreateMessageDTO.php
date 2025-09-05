<?php

namespace App\DTOs;

class CreateMessageDTO
{
    public function __construct(
        public readonly int $conversationId,
        public readonly string $content,
        public readonly string $senderType,
        public readonly ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'content' => $this->content,
            'sender_type' => $this->senderType,
            'metadata' => $this->metadata,
        ];
    }
}

# cGFuZ29saW4=
