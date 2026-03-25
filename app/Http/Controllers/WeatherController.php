<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeatherController extends Controller
{
    /**
     * Kuvab Dashboardi koos ilmaandmetega.
     */
    public function index(Request $request, WeatherService $weatherService): Response
    {
        $city = $request->string('city')->trim()->value() ?: 'Tallinn';
        $weatherResult = $weatherService->normalizeWeatherResult(
            $weatherService->getWeatherForCity($city),
            $city,
        );
        $markers = Marker::query()
            ->orderByDesc('added')
            ->get()
            ->map(fn (Marker $marker) => [
                'id' => $marker->id,
                'name' => $marker->name,
                'latitude' => $marker->latitude,
                'longitude' => $marker->longitude,
                'description' => $marker->description,
                'added' => $marker->added?->toDateTimeString(),
                'edited' => $marker->edited?->toDateTimeString(),
            ]);

        return Inertia::render('Dashboard', [
            'weather' => $weatherResult['weather'] ?? null,
            'city' => $city,
            'resolvedCity' => $weatherResult['resolved_city'] ?? $city,
            'weatherError' => $weatherResult['error'] ?? null,
            'markers' => $markers,
        ]);
    }
}
