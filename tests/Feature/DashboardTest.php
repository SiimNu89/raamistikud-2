<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    Http::fake([
        'api.openweathermap.org/geo/1.0/direct*' => Http::response([
            [
                'name' => 'Tallinn',
                'country' => 'EE',
                'lat' => 59.437,
                'lon' => 24.7536,
            ],
        ]),
        'api.openweathermap.org/*' => Http::response([
            'main' => ['temp' => 5.5],
            'weather' => [
                ['description' => 'pilves', 'icon' => '04d'],
            ],
        ]),
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});
