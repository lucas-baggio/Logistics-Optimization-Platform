<?php

namespace App\Logistics\Services;

use App\Logistics\DTOs\RouteDTO;
use App\Logistics\Models\RouteModel;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class RouteService
 *
 * Placeholder service for logistics route-related domain operations.
 */
class RouteService
{
    /**
     * Find a route by ID.
     *
     * @param int $id
     * @return RouteModel|null
     */
    public function find(int $id): ?RouteModel
    {
        return RouteModel::with('vehicle')->find($id);
    }

    /**
     * Create a new route from DTO.
     *
     * @param RouteDTO $dto
     * @return RouteModel
     */
    public function create(RouteDTO $dto): RouteModel
    {
        $model = RouteModel::create($dto->toArray());
        return $model;
    }

    /**
     * Update a route model from a DTO
     */
    public function update(RouteModel $model, RouteDTO $dto): RouteModel
    {
        $model->fill($dto->toArray());
        $model->save();
        return $model;
    }

    /**
     * Convert a model to DTO.
     *
     * @param RouteModel $model
     * @return RouteDTO
     */
    public function toDto(RouteModel $model): RouteDTO
    {
        return RouteDTO::fromModel($model);
    }

    /**
     * List routes (paginated) optionally filtered by vehicle id
     *
     * @param int|null $vehicleId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(?int $vehicleId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = RouteModel::with('vehicle');
        if ($vehicleId !== null) {
            $query->where('vehicle_id', $vehicleId);
        }
        return $query->paginate($perPage);
    }

    /**
     * Remove a route
     */
    public function delete(RouteModel $model): bool
    {
        return $model->delete();
    }
}
