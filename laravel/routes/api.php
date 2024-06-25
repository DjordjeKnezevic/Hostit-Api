<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\PricingController;
use App\Http\Controllers\Api\Admin\LocationController;
use App\Http\Controllers\Api\Admin\ServerTypeController;
use App\Http\Controllers\Api\Admin\TestimonialController;
use App\Http\Controllers\Api\Admin\ServerStatusController;
use App\Http\Controllers\Api\Admin\SubscriptionController;
use App\Http\Controllers\Api\Admin\ServerController as AdminServerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/check-email', [AuthController::class, 'checkUserByEmail']);

Route::get('/navigation-links', [GeneralController::class, 'getNavigationLinks']);
Route::get('/pricing-options', [GeneralController::class, 'getPricingOptions']);
Route::get('/testimonials', [GeneralController::class, 'getTestimonials']);
Route::get('/locations', [GeneralController::class, 'getLocations']);

Route::get('/server-options', [ServerController::class, 'getServerOptions']);
Route::get('/servers/{serverId}', [ServerController::class, 'getServerDetails']);
Route::get('/server_types/{serverTypeId}', [ServerController::class, 'getServerType']);
Route::get('/pricing', [ServerController::class, 'getServerPricing']);
Route::get('/locations/{locationId}', [ServerController::class, 'getLocationDetails']);
Route::get('/locations/{locationId}/servers', [ServerController::class, 'getServersByLocation']);

Route::middleware('jwt')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/process-renting', [ServerController::class, 'processRenting']);
    Route::get('/user-servers', [ProfileController::class, 'getUserServers']);

    Route::patch('/servers/{serverId}/start', [ProfileController::class, 'startServer']);
    Route::patch('/servers/{serverId}/stop', [ProfileController::class, 'stopServer']);
    Route::patch('/servers/{serverId}/restart', [ProfileController::class, 'restartServer']);
    Route::patch('/servers/{serverId}/terminate', [ProfileController::class, 'terminateServer']);
});

Route::middleware('jwt', 'admin')->prefix('admin')->group(function () {
    Route::apiResource('locations', LocationController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('testimonials', TestimonialController::class);
    Route::apiResource('servers', AdminServerController::class);
    Route::apiResource('server-statuses', ServerStatusController::class)->only(['index', 'show']);
    Route::apiResource('server-types', ServerTypeController::class);
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::apiResource('pricings', PricingController::class);
});
