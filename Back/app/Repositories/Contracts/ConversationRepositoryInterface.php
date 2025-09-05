<?php

namespace App\Repositories\Contracts;

use App\DTOs\CreateConversationDTO;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;

interface ConversationRepositoryInterface
{
    public function create(CreateConversationDTO $dto): Conversation;
    
    public function findById(int $id): ?Conversation;
    
    public function findByIdWithMessages(int $id): ?Conversation;
    
    public function getAll(): Collection;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
}
