<?php

namespace App\Logistics\DTOs;

use Carbon\Carbon;
use App\Logistics\Models\DeliveryPointModel;

class DeliveryPointDTO
{
    public function __construct(
        private ?int $id,
        private string $address,
        private float $latitude,
        private float $longitude,
        private float $weightKg,
        private string $timeWindowStart,
        private string $timeWindowEnd,
        private bool $isRouted = false,
        private ?int $routeId = null,
        private ?Carbon $createdAt = null,
        private ?Carbon $updatedAt = null
    ) {
    }

    public function getId(): ?int { return $this->id; }
    public function getAddress(): string { return $this->address; }
    public function getLatitude(): float { return $this->latitude; }
    public function getLongitude(): float { return $this->longitude; }
    public function getWeightKg(): float { return $this->weightKg; }
    public function getTimeWindowStart(): string { return $this->timeWindowStart; }
    public function getTimeWindowEnd(): string { return $this->timeWindowEnd; }
    public function isRouted(): bool { return $this->isRouted; }
    public function getRouteId(): ?int { return $this->routeId; }
    public function getCreatedAt(): ?Carbon { return $this->createdAt; }
    public function getUpdatedAt(): ?Carbon { return $this->updatedAt; }

    public static function fromModel(DeliveryPointModel $model): self
    {
        return new self(
            id: $model->getKey(),
            address: (string)$model->address,
            latitude: (float)$model->latitude,
            longitude: (float)$model->longitude,
            weightKg: (float)$model->weight_kg,
            timeWindowStart: (string)$model->time_window_start,
            timeWindowEnd: (string)$model->time_window_end,
            isRouted: (bool)$model->is_routed,
            routeId: $model->route_id ? (int)$model->route_id : null,
            createdAt: $model->created_at ? Carbon::parse($model->created_at) : null,
            updatedAt: $model->updated_at ? Carbon::parse($model->updated_at) : null
        );
    }

    public function toArray(): array
    {
        return [
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'weight_kg' => $this->weightKg,
            'time_window_start' => $this->timeWindowStart,
            'time_window_end' => $this->timeWindowEnd,
            'is_routed' => $this->isRouted,
            'route_id' => $this->routeId,
        ];
    }
}
