<?php

namespace App\Http\Controllers\Api;

use App\Models\Server;
use App\Models\Invoice;
use App\Models\Pricing;
use App\Models\Location;
use App\Models\ServerType;
use App\Models\ServerStatus;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ServerController extends Controller
{
    public function getServerOptions()
    {
        $locations = Location::with('servers.pricing', 'servers.serverType')->get();

        $locations->each(function ($location) {
            $location->servers = $location->servers->sortBy(function ($server) {
                return $server->serverType->cpu_cores;
            });
        });

        return response()->json($locations);
    }

    public function getServerDetails($serverId)
    {
        $server = Server::findOrFail($serverId);
        return response()->json($server);
    }

    public function getServerType($serverTypeId)
    {
        $serverType = ServerType::findOrFail($serverTypeId);
        return response()->json($serverType);
    }

    public function getServerPricing(Request $request)
    {
        $serverId = $request->query('service_id');
        $pricingPlans = Pricing::where('service_id', $serverId)
            ->where('service_type', 'App\Models\Server')
            ->get();

        return response()->json($pricingPlans);
    }

    public function getLocationDetails($locationId)
    {
        $location = Location::findOrFail($locationId);
        return response()->json($location);
    }

    public function getServersByLocation($locationId)
    {
        $servers = Server::with('serverType', 'pricing')->where('location_id', $locationId)->get();
        return response()->json($servers);
    }

    public function processRenting(Request $request)
    {
        $validatedData = $request->validate([
            'location' => 'required|exists:locations,id',
            'server' => 'required|exists:servers,id',
            'pricing' => 'required|exists:pricing,id',
        ]);

        DB::transaction(function () use ($validatedData) {
            $user = auth()->user();
            $server = Server::findOrFail($validatedData['server']);
            $pricing = Pricing::findOrFail($validatedData['pricing']);

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'service_id' => $server->id,
                'service_type' => 'App\Models\Server',
                'pricing_id' => $pricing->id,
                'start_date' => now(),
            ]);

            $dueDate = now();
            $status = 'pending';

            if ($pricing->period === 'hourly') {
                $dueDate = now()->endOfMonth();
            } else {
                $status = 'paid';
            }

            Invoice::create([
                'subscription_id' => $subscription->id,
                'amount_due' => 0,
                'amount_paid' => $pricing->period === 'hourly' ? 0 : $pricing->price,
                'due_date' => $dueDate,
                'status' => $status,
            ]);

            ServerStatus::create([
                'subscription_id' => $subscription->id,
                'status' => 'good',
                'last_started_at' => now(),
            ]);
        });

        return response()->json(['message' => 'Server rental successful.'], 200);
    }
}
