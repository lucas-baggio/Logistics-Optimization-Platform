<?php

namespace App\Logistics\Services;

use App\Logistics\DTOs\DeliveryPointDTO;
use App\Logistics\Models\DeliveryPointModel;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryPointService
{
    public function find(int $id): ?DeliveryPointModel
    {
        return DeliveryPointModel::with('route')->find($id);
    }

    public function create(DeliveryPointDTO $dto): DeliveryPointModel
    {
        return DeliveryPointModel::create($dto->toArray());
    }

    public function update(DeliveryPointModel $model, DeliveryPointDTO $dto): DeliveryPointModel
    {
        $model->fill($dto->toArray());
        $model->save();
        return $model;
    }

    public function toDto(DeliveryPointModel $model): DeliveryPointDTO
    {
        return DeliveryPointDTO::fromModel($model);
    }

    public function list(?int $routeId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = DeliveryPointModel::with('route');
        if ($routeId !== null) {
            $query->where('route_id', $routeId);
        }
        return $query->paginate($perPage);
    }

    public function assignToRoute(DeliveryPointModel $model, ?int $routeId): DeliveryPointModel
    {
        $model->route_id = $routeId;
        $model->is_routed = $routeId !== null;
        $model->save();
        return $model;
    }

    public function delete(DeliveryPointModel $model): bool
    {
        return $model->delete();
    }
}
