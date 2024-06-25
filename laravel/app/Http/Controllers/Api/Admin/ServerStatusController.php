<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServerStatus;
use Illuminate\Http\Request;

class ServerStatusController extends Controller
{
    public function index()
    {
        return ServerStatus::select('id', 'subscription_id', 'status', 'uptime', 'downtime', 'last_started_at', 'last_stopped_at', 'last_crashed_at', 'created_at', 'updated_at')->get();
    }

    public function show($id)
    {
        return ServerStatus::select('id', 'subscription_id', 'status', 'uptime', 'downtime', 'last_started_at', 'last_stopped_at', 'last_crashed_at', 'created_at', 'updated_at')->findOrFail($id);
    }
}
