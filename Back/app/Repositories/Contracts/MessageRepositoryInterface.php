<?php

namespace App\Repositories\Contracts;

use App\DTOs\CreateMessageDTO;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface MessageRepositoryInterface
{
    public function create(CreateMessageDTO $dto): Message;
    
    public function findById(int $id): ?Message;
    
    public function getByConversationId(int $conversationId): Collection;
    
    public function getLatestByConversationId(int $conversationId, int $limit = 10): Collection;
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
}
