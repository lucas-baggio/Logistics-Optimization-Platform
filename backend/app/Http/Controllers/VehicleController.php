<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Logistics\DTOs\VehicleDTO;
use App\Logistics\Services\VehicleService;
use App\Logistics\Models\VehicleModel;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    public function index(VehicleService $service): JsonResponse
    {
        return response()->json($service->list());
    }

    public function show(int $id, VehicleService $service): JsonResponse
    {
        $vehicle = $service->find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['data' => $vehicle]);
    }

    public function store(StoreVehicleRequest $request, VehicleService $service): JsonResponse
    {
        $data = $request->validated();
        $dto = new VehicleDTO(null, $data['name'], (float)$data['capacity_kg'], (int)$data['max_stops'], $data['available'] ?? true);
        $vehicle = $service->create($dto);
        return response()->json(['data' => $vehicle], 201);
    }

    public function update(UpdateVehicleRequest $request, int $id, VehicleService $service): JsonResponse
    {
        $vehicle = $service->find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validated();
        $dto = new VehicleDTO($vehicle->id, $data['name'] ?? $vehicle->name, (float)($data['capacity_kg'] ?? $vehicle->capacity_kg), (int)($data['max_stops'] ?? $vehicle->max_stops), $data['available'] ?? $vehicle->available);
        $vehicle = $service->update($vehicle, $dto);
        return response()->json(['data' => $vehicle]);
    }

    public function destroy(int $id, VehicleService $service): JsonResponse
    {
        $vehicle = $service->find($id);
        if (!$vehicle) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $service->delete($vehicle);
        return response()->json(['message' => 'Deleted']);
    }
}
