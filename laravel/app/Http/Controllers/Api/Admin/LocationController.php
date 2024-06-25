<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return Location::select('id', 'name', 'network_zone', 'city', 'image', 'created_at', 'updated_at')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'network_zone' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        $location = Location::create($validatedData);

        return response()->json($location, 201);
    }

    public function show($id)
    {
        return Location::select('id', 'name', 'network_zone', 'city', 'image', 'created_at', 'updated_at')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'network_zone' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        $location = Location::findOrFail($id);
        $location->update($validatedData);

        return response()->json($location);
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(null, 204);
    }
}
