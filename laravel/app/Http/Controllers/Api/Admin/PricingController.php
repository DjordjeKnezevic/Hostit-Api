<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pricing;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        return Pricing::with(['service:id,name'])
            ->select('id', 'name', 'service_type', 'service_id', 'price', 'period', 'valid_from', 'valid_until', 'created_at', 'updated_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
            'period' => 'required|string|in:hourly,monthly,yearly',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
        ]);

        $pricing = Pricing::create(array_merge($validatedData, ['service_type' => 'App\Models\Server']));

        return response()->json($pricing, 201);
    }

    public function show($id)
    {
        return Pricing::with(['service:id,name'])
            ->select('id', 'name', 'service_type', 'service_id', 'price', 'period', 'valid_from', 'valid_until', 'created_at', 'updated_at')
            ->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'service_id' => 'required|integer',
            'price' => 'required|numeric',
            'period' => 'required|string|in:hourly,monthly,yearly',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
        ]);

        $pricing = Pricing::findOrFail($id);
        $pricing->update($validatedData);

        return response()->json($pricing);
    }

    public function destroy($id)
    {
        $pricing = Pricing::findOrFail($id);
        $pricing->delete();

        return response()->json(null, 204);
    }
}
