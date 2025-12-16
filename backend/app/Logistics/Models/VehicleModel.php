<?php

namespace App\Logistics\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VehicleModel
 *
 * Eloquent model for vehicles table.
 */
class VehicleModel extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'name',
        'capacity_kg',
        'max_stops',
        'available',
    ];

    protected $casts = [
        'max_stops' => 'integer',
        'available' => 'boolean',
        'capacity_kg' => 'float',
    ];

    public $timestamps = true;

    /**
     * Convenience: create the model from a DTO.
     */
    public static function fromDTO(\App\Logistics\DTOs\VehicleDTO $dto): self
    {
        return self::create([
            'name' => $dto->getName(),
            'capacity_kg' => $dto->getCapacityKg(),
            'max_stops' => $dto->getMaxStops(),
            'available' => $dto->isAvailable(),
        ]);
    }
}
