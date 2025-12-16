<?php

namespace App\Logistics\DTOs;

use Carbon\Carbon;
use App\Logistics\Models\RouteModel;

class RouteDTO
{
    public function __construct(
        private ?int $id,
        private int $vehicleId,
        private string $status,
        private float $optimizedDistanceKm,
        private int $optimizedDurationMin,
        private array $optimizedSequence = [],
        private ?string $polylineData = null,
        private ?Carbon $createdAt = null,
        private ?Carbon $updatedAt = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehicleId(): int
    {
        return $this->vehicleId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOptimizedDistanceKm(): float
    {
        return $this->optimizedDistanceKm;
    }

    public function getOptimizedDurationMin(): int
    {
        return $this->optimizedDurationMin;
    }

    public function getOptimizedSequence(): array
    {
        return $this->optimizedSequence;
    }

    public function getPolylineData(): ?string
    {
        return $this->polylineData;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public static function fromModel(RouteModel $model): self
    {
        return new self(
            id: $model->getKey(),
            vehicleId: (int)$model->vehicle_id,
            status: (string)$model->status,
            optimizedDistanceKm: (float)$model->optimized_distance_km,
            optimizedDurationMin: (int)$model->optimized_duration_min,
            optimizedSequence: $model->optimized_sequence_json ?? [],
            polylineData: $model->polyline_data ?? null,
            createdAt: $model->created_at ? Carbon::parse($model->created_at) : null,
            updatedAt: $model->updated_at ? Carbon::parse($model->updated_at) : null
        );
    }

    public function toArray(): array
    {
        return [
            'vehicle_id' => $this->vehicleId,
            'status' => $this->status,
            'optimized_distance_km' => $this->optimizedDistanceKm,
            'optimized_duration_min' => $this->optimizedDurationMin,
            'optimized_sequence_json' => $this->optimizedSequence,
            'polyline_data' => $this->polylineData,
        ];
    }
}
