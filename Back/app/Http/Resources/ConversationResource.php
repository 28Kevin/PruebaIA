<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
            'latest_message' => new MessageResource($this->whenLoaded('latestMessage')),
            'messages_count' => $this->when(
                $this->relationLoaded('messages'),
                fn() => $this->messages->count()
            ),
        ];
    }
}
