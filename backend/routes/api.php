<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DeliveryPointController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/status', [StatusController::class, 'index']);

// Vehicles
Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/vehicles/{id}', [VehicleController::class, 'show']);
Route::post('/vehicles', [VehicleController::class, 'store']);
Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);

// Routes
Route::get('/routes', [RouteController::class, 'index']);
Route::get('/routes/{id}', [RouteController::class, 'show']);
Route::post('/routes', [RouteController::class, 'store']);
Route::put('/routes/{id}', [RouteController::class, 'update']);
Route::delete('/routes/{id}', [RouteController::class, 'destroy']);

// Delivery points
Route::get('/delivery-points', [DeliveryPointController::class, 'index']);
Route::get('/delivery-points/{id}', [DeliveryPointController::class, 'show']);
Route::post('/delivery-points', [DeliveryPointController::class, 'store']);
Route::put('/delivery-points/{id}', [DeliveryPointController::class, 'update']);
Route::post('/delivery-points/{id}/assign', [DeliveryPointController::class, 'assign']);
Route::delete('/delivery-points/{id}', [DeliveryPointController::class, 'destroy']);
