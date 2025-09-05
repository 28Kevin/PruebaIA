<?php

namespace App\DTOs;

class OpenAIResponseDTO
{
    public function __construct(
        public readonly string $content,
        public readonly ?array $toolCalls = null,
        public readonly ?string $finishReason = null,
        public readonly ?array $metadata = null
    ) {}

    public function hasToolCalls(): bool
    {
        return !empty($this->toolCalls);
    }

    public function getWeatherToolCall(): ?array
    {
        if (!$this->hasToolCalls()) {
            return null;
        }

        foreach ($this->toolCalls as $toolCall) {
            if ($toolCall['function']['name'] === 'get_weather_for_location') {
                return $toolCall;
            }
        }

        return null;
    }
}
