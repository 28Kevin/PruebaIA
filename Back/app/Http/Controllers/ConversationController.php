<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateConversationRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Services\Contracts\ChatServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(
        private ChatServiceInterface $chatService
    ) {}

    /**
     * Create a new conversation
     */
    public function store(CreateConversationRequest $request): JsonResponse
    {
        try {
            $conversation = $this->chatService->createConversation($request->input('title'));
            
            return response()->json([
                'success' => true,
                'message' => 'Conversación creada exitosamente',
                'data' => new ConversationResource($conversation)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la conversación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a conversation with all its messages
     */
    public function show(int $conversationId): JsonResponse
    {
        try {
            $conversation = $this->chatService->getConversation($conversationId);
            
            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversación no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new ConversationResource($conversation)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la conversación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message to a conversation
     */
    public function sendMessage(SendMessageRequest $request, int $conversationId): JsonResponse
    {
        try {
            $message = $this->chatService->sendMessage($conversationId, $request->input('content'));
            
            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'data' => new MessageResource($message)
            ], 201);

        } catch (Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}

# cGFuZ29saW4=
