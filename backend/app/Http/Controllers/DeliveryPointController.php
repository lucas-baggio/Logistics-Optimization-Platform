<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryPointRequest;
use App\Http\Requests\UpdateDeliveryPointRequest;
use App\Logistics\DTOs\DeliveryPointDTO;
use App\Logistics\Services\DeliveryPointService;
use App\Logistics\Models\DeliveryPointModel;
use Illuminate\Http\JsonResponse;

class DeliveryPointController extends Controller
{
    public function index(DeliveryPointService $service): JsonResponse
    {
        return response()->json($service->list());
    }

    public function show(int $id, DeliveryPointService $service): JsonResponse
    {
        $dp = $service->find($id);
        if (!$dp) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['data' => $service->toDto($dp)]);
    }

    public function store(StoreDeliveryPointRequest $request, DeliveryPointService $service): JsonResponse
    {
        $data = $request->validated();
        $dto = new DeliveryPointDTO(null, $data['address'], (float)$data['latitude'], (float)$data['longitude'], (float)$data['weight_kg'], $data['time_window_start'], $data['time_window_end'], $data['is_routed'] ?? false, $data['route_id'] ?? null);
        $dp = $service->create($dto);
        return response()->json(['data' => $service->toDto($dp)], 201);
    }

    public function update(UpdateDeliveryPointRequest $request, int $id, DeliveryPointService $service): JsonResponse
    {
        $dp = $service->find($id);
        if (!$dp) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $data = $request->validated();
        $dto = new DeliveryPointDTO($dp->id, $data['address'] ?? $dp->address, (float)($data['latitude'] ?? $dp->latitude), (float)($data['longitude'] ?? $dp->longitude), (float)($data['weight_kg'] ?? $dp->weight_kg), $data['time_window_start'] ?? $dp->time_window_start, $data['time_window_end'] ?? $dp->time_window_end, $data['is_routed'] ?? $dp->is_routed, $data['route_id'] ?? $dp->route_id);
        $dp = $service->update($dp, $dto);
        return response()->json(['data' => $service->toDto($dp)]);
    }

    public function assign(int $id, DeliveryPointService $service): JsonResponse
    {
        $dp = $service->find($id);
        if (!$dp) {
            return response()->json(['message' => 'Not found'], 404);
        }
        // expects `route_id` in request body
        $routeId = request()->input('route_id');
        $dp = $service->assignToRoute($dp, $routeId);
        return response()->json(['data' => $service->toDto($dp)]);
    }

    public function destroy(int $id, DeliveryPointService $service): JsonResponse
    {
        $dp = $service->find($id);
        if (!$dp) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $service->delete($dp);
        return response()->json(['message' => 'Deleted']);
    }
}
