<?php

namespace App\Logistics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryPointModel extends Model
{
    protected $table = 'delivery_points';

    protected $fillable = [
        'address',
        'latitude',
        'longitude',
        'weight_kg',
        'time_window_start',
        'time_window_end',
        'is_routed',
        'route_id',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'weight_kg' => 'float',
        'is_routed' => 'boolean',
        'route_id' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Relationship to route
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(RouteModel::class, 'route_id');
    }

    /**
     * Create from DTO
     */
    public static function fromDTO(\App\Logistics\DTOs\DeliveryPointDTO $dto): self
    {
        return self::create($dto->toArray());
    }
}
