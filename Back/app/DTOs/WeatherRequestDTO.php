<?php

namespace App\DTOs;

class WeatherRequestDTO
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly ?string $location = null,
        public readonly array $currentWeather = ['temperature_2m', 'relative_humidity_2m', 'weather_code'],
        public readonly array $hourlyWeather = [],
        public readonly array $dailyWeather = []
    ) {}

    public function toArray(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'current' => implode(',', $this->currentWeather),
            'hourly' => !empty($this->hourlyWeather) ? implode(',', $this->hourlyWeather) : null,
            'daily' => !empty($this->dailyWeather) ? implode(',', $this->dailyWeather) : null,
            'timezone' => 'auto',
        ];
    }
}
