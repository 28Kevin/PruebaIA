<?php

namespace App\Providers;

use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use App\Services\Contracts\ChatServiceInterface;
use App\Services\Contracts\OpenAIServiceInterface;
use App\Services\Contracts\WeatherServiceInterface;
use App\Services\ChatService;
use App\Services\GroqService;
use App\Services\WeatherService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);

        // Service bindings - Using Groq as free alternative
        $this->app->bind(OpenAIServiceInterface::class, GroqService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherService::class);
        $this->app->bind(ChatServiceInterface::class, ChatService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
