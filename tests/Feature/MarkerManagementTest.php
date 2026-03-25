<?php

use App\Models\Marker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can create a marker', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('markers.store'), [
            'name' => 'Tallinn Old Town',
            'latitude' => 59.437,
            'longitude' => 24.745,
            'description' => 'UNESCO old town marker',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('markers', [
        'name' => 'Tallinn Old Town',
        'latitude' => 59.4370000,
        'longitude' => 24.7450000,
    ]);
});

test('authenticated users can update a marker', function () {
    $user = User::factory()->create();
    $marker = Marker::create([
        'name' => 'Original',
        'latitude' => 59.1,
        'longitude' => 24.1,
        'description' => 'Before update',
    ]);

    $response = $this
        ->actingAs($user)
        ->put(route('markers.update', $marker), [
            'name' => 'Updated name',
            'latitude' => 58.5,
            'longitude' => 25.5,
            'description' => 'After update',
        ]);

    $response->assertRedirect();

    expect($marker->fresh())
        ->name->toBe('Updated name')
        ->latitude->toBe(58.5)
        ->longitude->toBe(25.5)
        ->description->toBe('After update');
});

test('authenticated users can delete a marker', function () {
    $user = User::factory()->create();
    $marker = Marker::create([
        'name' => 'Delete me',
        'latitude' => 59.2,
        'longitude' => 24.2,
        'description' => 'Disposable marker',
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('markers.destroy', $marker));

    $response->assertRedirect();

    $this->assertDatabaseMissing('markers', [
        'id' => $marker->id,
    ]);
});
