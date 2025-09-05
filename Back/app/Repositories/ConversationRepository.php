<?php

namespace App\Repositories;

use App\DTOs\CreateConversationDTO;
use App\Models\Conversation;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function create(CreateConversationDTO $dto): Conversation
    {
        return Conversation::create($dto->toArray());
    }

    public function findById(int $id): ?Conversation
    {
        return Conversation::find($id);
    }

    public function findByIdWithMessages(int $id): ?Conversation
    {
        return Conversation::with('messages')->find($id);
    }

    public function getAll(): Collection
    {
        return Conversation::with('latestMessage')->orderBy('updated_at', 'desc')->get();
    }

    public function update(int $id, array $data): bool
    {
        return Conversation::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Conversation::destroy($id) > 0;
    }
}

# cGFuZ29saW4=
