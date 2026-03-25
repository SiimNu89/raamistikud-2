<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MarkerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMarker($request);

        Marker::create($data);

        return back()->with('success', 'Marker added successfully.');
    }

    public function update(Request $request, Marker $marker): RedirectResponse
    {
        $data = $this->validateMarker($request);

        $marker->update($data);

        return back()->with('success', 'Marker updated successfully.');
    }

    public function destroy(Marker $marker): RedirectResponse
    {
        $marker->delete();

        return back()->with('success', 'Marker deleted successfully.');
    }

    protected function validateMarker(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
