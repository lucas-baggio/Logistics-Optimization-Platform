<?php

namespace App\Logistics\Services;

use App\Logistics\DTOs\VehicleDTO;
use App\Logistics\Models\VehicleModel;
use Illuminate\Pagination\LengthAwarePaginator;

class VehicleService
{
    public function find(int $id): ?VehicleModel
    {
        return VehicleModel::find($id);
    }

    public function create(VehicleDTO $dto): VehicleModel
    {
        return VehicleModel::fromDTO($dto);
    }

    public function update(VehicleModel $model, VehicleDTO $dto): VehicleModel
    {
        $model->fill($dto->toArray());
        $model->save();
        return $model;
    }

    public function delete(VehicleModel $model): bool
    {
        return $model->delete();
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return VehicleModel::paginate($perPage);
    }
}
