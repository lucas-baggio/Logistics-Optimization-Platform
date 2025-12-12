<?php

namespace App\Logistics\DTOs;

/**
 * Class RouteDTO
 *
 * Domain Data Transfer Object for a logistics route.
 */
class RouteDTO
{
    public function __construct(
        private string $origin,
        private string $destination,
        private float $distance
    ) {
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }
}
