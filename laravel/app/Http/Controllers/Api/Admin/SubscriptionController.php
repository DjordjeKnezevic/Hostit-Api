<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Subscription::with(['user:id,email', 'pricing:id,name,period', 'service:id,name'])
            ->select('id', 'user_id', 'service_id', 'service_type', 'pricing_id', 'start_date', 'end_date', 'created_at', 'updated_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:servers,id',
            'pricing_id' => 'required|exists:pricing,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $subscription = Subscription::create(array_merge($validatedData, ['service_type' => 'App\Models\Server']));

        return response()->json($subscription, 201);
    }

    public function show($id)
    {
        return Subscription::with(['user:id,email', 'pricing:id,name,period', 'service:id,name'])
            ->select('id', 'user_id', 'service_id', 'service_type', 'pricing_id', 'start_date', 'end_date', 'created_at', 'updated_at')
            ->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:servers,id',
            'pricing_id' => 'required|exists:pricing,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $subscription = Subscription::findOrFail($id);
        $subscription->update($validatedData);

        return response()->json($subscription);
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return response()->json(null, 204);
    }
}
