<?php

namespace App\Repositories;

use App\DTOs\CreateMessageDTO;
use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function create(CreateMessageDTO $dto): Message
    {
        return Message::create($dto->toArray());
    }

    public function findById(int $id): ?Message
    {
        return Message::find($id);
    }

    public function getByConversationId(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();
    }

    public function getLatestByConversationId(int $conversationId, int $limit = 10): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    public function update(int $id, array $data): bool
    {
        return Message::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Message::destroy($id) > 0;
    }
}

# cGFuZ29saW4=
