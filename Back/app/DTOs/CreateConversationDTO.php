<?php

namespace App\DTOs;

class CreateConversationDTO
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?array $metadata = null
    ) {}

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'metadata' => $this->metadata,
        ];
    }
}

# cGFuZ29saW4=
