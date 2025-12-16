<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Logistics\DTOs\RouteDTO;
use App\Logistics\Services\RouteService;
use App\Logistics\Models\RouteModel;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    public function index(RouteService $service): JsonResponse
    {
        return response()->json($service->list());
    }

    public function show(int $id, RouteService $service): JsonResponse
    {
        $route = $service->find($id);
        if (!$route) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['data' => $service->toDto($route)]);
    }

    public function store(StoreRouteRequest $request, RouteService $service): JsonResponse
    {
        $data = $request->validated();
        $dto = new RouteDTO(null, $data['vehicle_id'], $data['status'], (float)$data['optimized_distance_km'], (int)$data['optimized_duration_min'], $data['optimized_sequence_json'], $data['polyline_data'] ?? null);
        $route = $service->create($dto);
        return response()->json(['data' => $service->toDto($route)], 201);
    }

    public function update(UpdateRouteRequest $request, int $id, RouteService $service): JsonResponse
    {
        $route = $service->find($id);
        if (!$route) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validated();
        $dto = new RouteDTO($route->id, $data['vehicle_id'] ?? $route->vehicle_id, $data['status'] ?? $route->status, (float)($data['optimized_distance_km'] ?? $route->optimized_distance_km), (int)($data['optimized_duration_min'] ?? $route->optimized_duration_min), $data['optimized_sequence_json'] ?? $route->optimized_sequence_json, $data['polyline_data'] ?? $route->polyline_data);
        $route = $service->update($route, $dto);
        return response()->json(['data' => $service->toDto($route)]);
    }

    public function destroy(int $id, RouteService $service): JsonResponse
    {
        $route = $service->find($id);
        if (!$route) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $service->delete($route);
        return response()->json(['message' => 'Deleted']);
    }
}
