<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\Location;
use App\Models\ServerType;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        return Server::with(['location:id,name', 'serverType:id,name'])
            ->select('id', 'name', 'location_id', 'server_type_id', 'created_at', 'updated_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'server_type_id' => 'required|exists:server_types,id',
        ]);

        $server = Server::create($validatedData);

        return response()->json($server, 201);
    }

    public function show($id)
    {
        return Server::with(['location:id,name', 'serverType:id,name'])
            ->select('id', 'name', 'location_id', 'server_type_id', 'created_at', 'updated_at')
            ->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'server_type_id' => 'required|exists:server_types,id',
        ]);

        $server = Server::findOrFail($id);
        $server->update($validatedData);

        return response()->json($server);
    }

    public function destroy($id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return response()->json(null, 204);
    }

    public function getLocations()
    {
        return Location::select('id', 'name')->get();
    }

    public function getServerTypes()
    {
        return ServerType::select('id', 'name')->get();
    }
}
