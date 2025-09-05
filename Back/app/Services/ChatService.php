<?php

namespace App\Services;

use App\DTOs\CreateConversationDTO;
use App\DTOs\CreateMessageDTO;
use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Services\Contracts\ChatServiceInterface;
use App\Services\Contracts\OpenAIServiceInterface;
use App\Services\Contracts\WeatherServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatService implements ChatServiceInterface
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository,
        private MessageRepositoryInterface $messageRepository,
        private OpenAIServiceInterface $openAIService,
        private WeatherServiceInterface $weatherService
    ) {}

    public function createConversation(?string $title = null): Conversation
    {
        $dto = new CreateConversationDTO(title: $title);
        return $this->conversationRepository->create($dto);
    }

    public function getConversation(int $conversationId): ?Conversation
    {
        return $this->conversationRepository->findByIdWithMessages($conversationId);
    }

    public function sendMessage(int $conversationId, string $content): Message
    {
        return DB::transaction(function () use ($conversationId, $content) {
            // Verify conversation exists
            $conversation = $this->conversationRepository->findById($conversationId);
            if (!$conversation) {
                throw new \Exception('Conversation not found');
            }

            // Save user message
            $userMessageDto = new CreateMessageDTO(
                conversationId: $conversationId,
                content: $content,
                senderType: 'user'
            );
            $userMessage = $this->messageRepository->create($userMessageDto);

            // Get conversation history for context
            $conversationHistory = $this->messageRepository->getLatestByConversationId($conversationId, 10);
            $historyArray = $conversationHistory->map(function ($message) {
                return [
                    'content' => $message->content,
                    'sender_type' => $message->sender_type
                ];
            })->toArray();

            try {
                // Send message to OpenAI
                $aiResponse = $this->openAIService->sendMessage($content, $historyArray);

                // Check if AI wants to use weather tool
                if ($aiResponse->hasToolCalls()) {
                    $weatherToolCall = $aiResponse->getWeatherToolCall();
                    if ($weatherToolCall) {
                        $assistantMessage = $this->handleWeatherToolCall($conversationId, $content, $weatherToolCall, $historyArray);
                    } else {
                        // Handle other tool calls or fallback
                        $assistantMessage = $this->createAssistantMessage($conversationId, $aiResponse->content ?: 'Lo siento, no pude procesar tu solicitud.');
                    }
                } else {
                    // Direct response without tools
                    $assistantMessage = $this->createAssistantMessage($conversationId, $aiResponse->content);
                }

                // Update conversation title if it's the first exchange
                if ($conversationHistory->count() <= 1 && !$conversation->title) {
                    $title = $this->generateConversationTitle($content);
                    $this->conversationRepository->update($conversationId, ['title' => $title]);
                }

                return $assistantMessage;

            } catch (\Exception $e) {
                Log::error('Chat Service Error: ' . $e->getMessage());
                
                // Create error response message
                $errorMessage = 'Lo siento, he tenido un problema técnico. Por favor, inténtalo de nuevo.';
                return $this->createAssistantMessage($conversationId, $errorMessage);
            }
        });
    }

    public function sendMessageWithFallback(int $conversationId, string $content): Message
    {
        return DB::transaction(function () use ($conversationId, $content) {
            // Verify conversation exists
            $conversation = $this->conversationRepository->findById($conversationId);
            if (!$conversation) {
                throw new \Exception('Conversation not found');
            }

            // Save user message
            $userMessageDto = new CreateMessageDTO(
                conversationId: $conversationId,
                content: $content,
                senderType: 'user'
            );
            $userMessage = $this->messageRepository->create($userMessageDto);

            // Generate fallback response based on content
            $fallbackResponse = $this->generateFallbackResponse($content);
            $assistantMessage = $this->createAssistantMessage($conversationId, $fallbackResponse);

            // Update conversation title if it's the first exchange
            $conversationHistory = $this->messageRepository->getLatestByConversationId($conversationId, 10);
            if ($conversationHistory->count() <= 1 && !$conversation->title) {
                $title = $this->generateConversationTitle($content);
                $this->conversationRepository->update($conversationId, ['title' => $title]);
            }

            return $assistantMessage;
        });
    }

    private function generateFallbackResponse(string $content): string
    {
        $content = strtolower($content);
        
        // Weather-related keywords
        if (str_contains($content, 'clima') || str_contains($content, 'tiempo') || str_contains($content, 'temperatura')) {
            return "🌤️ Hola! Soy MeteoBot, tu asistente del clima. Me encantaría ayudarte con información meteorológica, pero temporalmente tengo limitaciones técnicas. \n\n¿Podrías ser más específico sobre qué ciudad te interesa? Mientras tanto, te recomiendo revisar el pronóstico local. ☀️";
        }
        
        if (str_contains($content, 'lluvia') || str_contains($content, 'llover')) {
            return "🌧️ ¡Excelente pregunta sobre la lluvia! Como MeteoBot, normalmente consulto datos meteorológicos en tiempo real, pero ahora mismo tengo limitaciones técnicas. \n\nTe sugiero revisar apps como AccuWeather o Weather.com para información precisa sobre precipitaciones. 🌦️";
        }
        
        if (str_contains($content, 'hola') || str_contains($content, 'buenos') || str_contains($content, 'saludos')) {
            return "¡Hola! 👋 Soy MeteoBot, tu asistente especializado en clima y meteorología. \n\n¿En qué puedo ayudarte hoy? Puedes preguntarme sobre:\n• Temperatura actual\n• Pronóstico del tiempo\n• Condiciones climáticas\n• Recomendaciones según el clima ☀️🌧️❄️";
        }
        
        // Default response
        return "🤖 ¡Hola! Soy MeteoBot, especializado en información meteorológica. \n\nAunque temporalmente tengo limitaciones técnicas, estaré encantado de ayudarte con temas relacionados al clima cuando esté completamente operativo. \n\n¿Hay algo específico sobre el tiempo que te gustaría saber? 🌤️";
    }

    private function handleWeatherToolCall(int $conversationId, string $originalMessage, array $toolCall, array $conversationHistory): Message
    {
        try {
            $arguments = json_decode($toolCall['function']['arguments'], true);
            $latitude = $arguments['latitude'];
            $longitude = $arguments['longitude'];
            $location = $arguments['location'] ?? null;

            // Get weather data
            $weatherData = $this->weatherService->getWeatherForLocation($latitude, $longitude, $location);

            // Prepare tool result for OpenAI
            $toolResult = [
                'tool_call' => $toolCall,
                'result' => $weatherData
            ];

            // Send weather data back to OpenAI for natural language response
            $finalResponse = $this->openAIService->sendMessageWithToolResult($originalMessage, $toolResult, $conversationHistory);

            return $this->createAssistantMessage($conversationId, $finalResponse->content);

        } catch (\Exception $e) {
            Log::error('Weather Tool Call Error: ' . $e->getMessage());
            $errorMessage = 'Lo siento, no pude obtener la información del clima en este momento. Por favor, inténtalo más tarde.';
            return $this->createAssistantMessage($conversationId, $errorMessage);
        }
    }

    private function createAssistantMessage(int $conversationId, string $content): Message
    {
        $assistantMessageDto = new CreateMessageDTO(
            conversationId: $conversationId,
            content: $content,
            senderType: 'assistant'
        );

        return $this->messageRepository->create($assistantMessageDto);
    }

    private function generateConversationTitle(string $firstMessage): string
    {
        // Simple title generation based on first message
        $title = trim($firstMessage);
        if (strlen($title) > 50) {
            $title = substr($title, 0, 47) . '...';
        }
        return $title ?: 'Nueva conversación';
    }
}

# cGFuZ29saW4=
