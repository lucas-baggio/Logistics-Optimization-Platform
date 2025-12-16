<?php

namespace App\Logistics\DTOs;

use Carbon\Carbon;
use App\Logistics\Models\VehicleModel;

/**
 * Class VehicleDTO
 *
 * Domain Data Transfer Object for Vehicle entity.
 */
class VehicleDTO
{
    public function __construct(
        private ?int $id,
        private string $name,
        private float $capacityKg,
        private int $maxStops,
        private bool $available = true,
        private ?Carbon $createdAt = null,
        private ?Carbon $updatedAt = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCapacityKg(): float
    {
        return $this->capacityKg;
    }

    public function getMaxStops(): int
    {
        return $this->maxStops;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    /**
     * Build the DTO from an Eloquent model instance
     */
    public static function fromModel(VehicleModel $model): self
    {
        return new self(
            id: $model->getKey(),
            name: (string)$model->name,
            capacityKg: (float)$model->capacity_kg,
            maxStops: (int)$model->max_stops,
            available: (bool)$model->available,
            createdAt: $model->created_at ? Carbon::parse($model->created_at) : null,
            updatedAt: $model->updated_at ? Carbon::parse($model->updated_at) : null
        );
    }

    /**
     * Convert DTO into an array suitable for Eloquent mass assignment
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'capacity_kg' => $this->capacityKg,
            'max_stops' => $this->maxStops,
            'available' => $this->available,
        ];
    }
}
