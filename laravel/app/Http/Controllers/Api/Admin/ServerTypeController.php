<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServerType;
use Illuminate\Http\Request;

class ServerTypeController extends Controller
{
    public function index()
    {
        return ServerType::select('id', 'name', 'cpu_cores', 'ram', 'storage', 'network_speed', 'created_at', 'updated_at')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cpu_cores' => 'required|integer',
            'ram' => 'required|integer',
            'storage' => 'required|integer',
            'network_speed' => 'required|integer|max:255',
        ]);

        $serverType = ServerType::create($validatedData);

        return response()->json($serverType, 201);
    }

    public function show($id)
    {
        return ServerType::select('id', 'name', 'cpu_cores', 'ram', 'storage', 'network_speed', 'created_at', 'updated_at')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cpu_cores' => 'required|integer',
            'ram' => 'required|integer',
            'storage' => 'required|integer',
            'network_speed' => 'required|integer|max:255',
        ]);

        $serverType = ServerType::findOrFail($id);
        $serverType->update($validatedData);

        return response()->json($serverType);
    }

    public function destroy($id)
    {
        $serverType = ServerType::findOrFail($id);
        $serverType->delete();

        return response()->json(null, 204);
    }
}
