<?php

namespace App\Logistics\Services;

use App\Logistics\DTOs\RouteDTO;
use App\Logistics\Models\RouteModel;

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
        return RouteModel::find($id);
    }

    /**
     * Create a new route from DTO.
     *
     * @param RouteDTO $dto
     * @return RouteModel
     */
    public function create(RouteDTO $dto): RouteModel
    {
        $model = new RouteModel();
        $model->origin = $dto->getOrigin();
        $model->destination = $dto->getDestination();
        $model->distance = $dto->getDistance();
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
        return new RouteDTO(
            origin: $model->origin,
            destination: $model->destination,
            distance: $model->distance
        );
    }
}
