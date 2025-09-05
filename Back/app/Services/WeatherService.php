<?php

namespace App\Services;

use App\DTOs\WeatherRequestDTO;
use App\Services\Contracts\WeatherServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService implements WeatherServiceInterface
{
    private string $baseUrl;
    private string $geocodingUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.open_meteo.base_url');
        $this->geocodingUrl = 'https://geocoding-api.open-meteo.com/v1/search';
    }

    public function getWeatherForLocation(float $latitude, float $longitude, ?string $location = null): array
    {
        try {
            $weatherDto = new WeatherRequestDTO(
                latitude: $latitude,
                longitude: $longitude,
                location: $location,
                currentWeather: [
                    'temperature_2m',
                    'relative_humidity_2m',
                    'apparent_temperature',
                    'weather_code',
                    'wind_speed_10m',
                    'wind_direction_10m',
                    'pressure_msl'
                ]
            );

            $response = Http::get($this->baseUrl . '/forecast', array_filter($weatherDto->toArray()));

            if (!$response->successful()) {
                throw new \Exception('Weather API request failed: ' . $response->status());
            }

            $data = $response->json();

            return [
                'location' => $location ?? "Lat: {$latitude}, Lon: {$longitude}",
                'current' => [
                    'temperature' => $data['current']['temperature_2m'] ?? null,
                    'apparent_temperature' => $data['current']['apparent_temperature'] ?? null,
                    'humidity' => $data['current']['relative_humidity_2m'] ?? null,
                    'weather_code' => $data['current']['weather_code'] ?? null,
                    'weather_description' => $this->getWeatherDescription($data['current']['weather_code'] ?? 0),
                    'wind_speed' => $data['current']['wind_speed_10m'] ?? null,
                    'wind_direction' => $data['current']['wind_direction_10m'] ?? null,
                    'pressure' => $data['current']['pressure_msl'] ?? null,
                ],
                'units' => $data['current_units'] ?? [],
                'timezone' => $data['timezone'] ?? null,
                'timestamp' => $data['current']['time'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage());
            throw new \Exception('Error fetching weather data: ' . $e->getMessage());
        }
    }

    public function getCoordinatesFromLocation(string $location): array
    {
        try {
            $response = Http::get($this->geocodingUrl, [
                'name' => $location,
                'count' => 1,
                'language' => 'es',
                'format' => 'json'
            ]);

            if (!$response->successful()) {
                throw new \Exception('Geocoding API request failed: ' . $response->status());
            }

            $data = $response->json();

            if (empty($data['results'])) {
                throw new \Exception('Location not found: ' . $location);
            }

            $result = $data['results'][0];

            return [
                'latitude' => $result['latitude'],
                'longitude' => $result['longitude'],
                'name' => $result['name'],
                'country' => $result['country'] ?? null,
                'admin1' => $result['admin1'] ?? null,
                'timezone' => $result['timezone'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Geocoding API Error: ' . $e->getMessage());
            throw new \Exception('Error finding location coordinates: ' . $e->getMessage());
        }
    }

    private function getWeatherDescription(int $weatherCode): string
    {
        $descriptions = [
            0 => 'Cielo despejado',
            1 => 'Principalmente despejado',
            2 => 'Parcialmente nublado',
            3 => 'Nublado',
            45 => 'Niebla',
            48 => 'Niebla con escarcha',
            51 => 'Llovizna ligera',
            53 => 'Llovizna moderada',
            55 => 'Llovizna intensa',
            56 => 'Llovizna helada ligera',
            57 => 'Llovizna helada intensa',
            61 => 'Lluvia ligera',
            63 => 'Lluvia moderada',
            65 => 'Lluvia intensa',
            66 => 'Lluvia helada ligera',
            67 => 'Lluvia helada intensa',
            71 => 'Nieve ligera',
            73 => 'Nieve moderada',
            75 => 'Nieve intensa',
            77 => 'Granizo',
            80 => 'Chubascos ligeros',
            81 => 'Chubascos moderados',
            82 => 'Chubascos intensos',
            85 => 'Chubascos de nieve ligeros',
            86 => 'Chubascos de nieve intensos',
            95 => 'Tormenta',
            96 => 'Tormenta con granizo ligero',
            99 => 'Tormenta con granizo intenso',
        ];

        return $descriptions[$weatherCode] ?? 'Condición desconocida';
    }
}

# cGFuZ29saW4=
