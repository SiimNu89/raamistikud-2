<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getWeatherForCity(string $city): array
    {
        $apiKey = config('services.openweather.key');

        if (! is_string($apiKey) || blank($apiKey)) {
            return [
                'weather' => null,
                'resolved_city' => null,
                'error' => 'OpenWeather API voti pole seadistatud.',
            ];
        }

        $normalizedCity = trim($city);

        if ($normalizedCity === '') {
            return [
                'weather' => null,
                'resolved_city' => null,
                'error' => 'Sisesta linna nimi.',
            ];
        }

        $cacheKey = 'weather_'.strtolower(str_replace(' ', '_', $normalizedCity));

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($normalizedCity, $apiKey) {
            $geoResponse = Http::acceptJson()
                ->timeout(10)
                ->get('https://api.openweathermap.org/geo/1.0/direct', [
                    'q' => $normalizedCity,
                    'limit' => 1,
                    'appid' => $apiKey,
                ]);

            if (! $geoResponse->successful()) {
                return [
                    'weather' => null,
                    'resolved_city' => null,
                    'error' => 'Ilmaandmete asukohaotsing ebaonnestus.',
                ];
            }

            $location = $geoResponse->json()[0] ?? null;

            if (! is_array($location) || ! isset($location['lat'], $location['lon'])) {
                return [
                    'weather' => null,
                    'resolved_city' => null,
                    'error' => 'Selle nimega linna ei leitud.',
                ];
            }

            $weatherResponse = Http::acceptJson()
                ->timeout(10)
                ->get('https://api.openweathermap.org/data/2.5/weather', [
                    'lat' => $location['lat'],
                    'lon' => $location['lon'],
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'et',
                ]);

            if (! $weatherResponse->successful()) {
                return [
                    'weather' => null,
                    'resolved_city' => null,
                    'error' => 'Ilmaandmete laadimine ebaonnestus.',
                ];
            }

            $resolvedName = collect([
                $location['name'] ?? null,
                $location['state'] ?? null,
                $location['country'] ?? null,
            ])->filter()->implode(', ');

            return [
                'weather' => $weatherResponse->json(),
                'resolved_city' => $resolvedName !== '' ? $resolvedName : $normalizedCity,
                'error' => null,
            ];
        });
    }

    public function normalizeWeatherResult(mixed $result, string $fallbackCity): array
    {
        if (! is_array($result)) {
            return [
                'weather' => null,
                'resolved_city' => null,
                'error' => 'Ilmaandmeid ei onnestunud laadida.',
            ];
        }

        if (array_key_exists('weather', $result) || array_key_exists('resolved_city', $result) || array_key_exists('error', $result)) {
            return [
                'weather' => $result['weather'] ?? null,
                'resolved_city' => $result['resolved_city'] ?? $fallbackCity,
                'error' => $result['error'] ?? null,
            ];
        }

        return [
            'weather' => $result,
            'resolved_city' => $fallbackCity,
            'error' => null,
        ];
    }
}
