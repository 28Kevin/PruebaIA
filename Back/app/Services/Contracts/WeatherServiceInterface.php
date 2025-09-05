<?php

namespace App\Services\Contracts;

use App\DTOs\WeatherRequestDTO;

interface WeatherServiceInterface
{
    public function getWeatherForLocation(float $latitude, float $longitude, ?string $location = null): array;
    
    public function getCoordinatesFromLocation(string $location): array;
}
