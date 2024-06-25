<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Server;
use App\Models\Invoice;
use App\Models\Pricing;
use App\Models\Location;
use App\Models\ServerStatus;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getUserServers(Request $request)
    {
        $user = auth()->user();

        $stateFilter = $request->input('state');
        $locationFilter = $request->input('location');
        $sortOrder = $request->input('sort', 'desc');

        $servers = $this->fetchUserServers($user->id, $stateFilter, $locationFilter, $sortOrder);

        $locations = Location::all();
        $states = ServerStatus::STATUSES;

        return response()->json([
            'servers' => $servers,
            'locations' => $locations,
            'states' => $states
        ]);
    }

    protected function fetchUserServers($userId, $stateFilter = null, $locationFilter = null, $sortOrder = 'desc')
    {
        $servers = Subscription::where('user_id', $userId)
            ->where('service_type', 'App\Models\Server')
            ->with(['service.serverType', 'pricing', 'serverStatus'])
            ->when($stateFilter, function ($query, $stateFilter) {
                return $query->whereHas('serverStatus', function ($query) use ($stateFilter) {
                    return $query->where('status', $stateFilter);
                });
            })
            ->when($locationFilter, function ($query, $locationFilter) {
                return $query->whereHas('service', function ($query) use ($locationFilter) {
                    return $query->where('location_id', $locationFilter);
                });
            })
            ->orderBy('start_date', $sortOrder)
            ->paginate(3);

        return $servers;
    }

    public function startServer(Request $request, $serverId)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($serverId);
        $status = $subscription->serverStatus;
        $now = Carbon::now();

        if ($status->last_stopped_at) {
            $downtime = $now->diffInSeconds($status->last_stopped_at);
            $status->increment('downtime', $downtime);
        }

        $status->update([
            'status' => 'good',
            'last_started_at' => $now,
            'last_stopped_at' => null,
        ]);

        return response()->json(['success' => 'Server started successfully']);
    }

    public function stopServer(Request $request, $serverId)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($serverId);
        $status = $subscription->serverStatus;
        $now = Carbon::now();

        if ($status->last_started_at) {
            $uptime = $now->diffInSeconds($status->last_started_at);
            $status->increment('uptime', $uptime);
        }

        $status->update([
            'status' => 'stopped',
            'last_stopped_at' => $now,
        ]);

        return response()->json(['success' => 'Server stopped successfully']);
    }

    public function restartServer(Request $request, $serverId)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($serverId);
        $subscription->serverStatus->update(['status' => 'pending']);

        return response()->json(['success' => 'Server restart initiated']);
    }

    public function terminateServer(Request $request, $serverId)
    {
        $subscription = Subscription::where('user_id', Auth::id())->findOrFail($serverId);
        $pricing = $subscription->pricing;
        $startDate = Carbon::parse($subscription->start_date);

        $endDate = now();
        if ($pricing->period === 'monthly') {
            $endDate = $startDate->addMonth();
        } elseif ($pricing->period === 'yearly') {
            $endDate = $startDate->addYear();
        }

        DB::transaction(function () use ($subscription, $endDate) {
            $subscription->update(['end_date' => $endDate]);
            $subscription->serverStatus->update(['status' => 'terminated']);
        });

        return response()->json(['success' => 'Server terminated successfully']);
    }
}
