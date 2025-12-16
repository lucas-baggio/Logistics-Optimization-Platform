<?php

namespace App\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class RouteModel
 *
 * Example Eloquent model for Logistics routes. Stored in the app/Logistics/Models namespace to keep
 * domain concerns organized.
 */
class RouteModel extends Model
{
    // If you prefer to keep Eloquent models inside App\Models, you can point to this namespace
    // or add aliases. For small projects it's okay to keep domain models in App\Logistics\Models.

    // Table name (optional, based on your migration file)
    protected $table = 'routes';

    protected $fillable = [
        'vehicle_id',
        'status',
        'optimized_distance_km',
        'optimized_duration_min',
        'optimized_sequence_json',
        'polyline_data',
    ];

    protected $casts = [
        'optimized_distance_km' => 'float',
        'optimized_duration_min' => 'integer',
        'optimized_sequence_json' => 'array',
    ];

    public $timestamps = true;

    /**
     * Relationship to vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_id');
    }

    /**
     * Create from a DTO
     */
    public static function fromDTO(\App\Logistics\DTOs\RouteDTO $dto): self
    {
        return self::create($dto->toArray());
    }
}
