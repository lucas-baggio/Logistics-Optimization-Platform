<?php

namespace App\Logistics\Models;

use Illuminate\Database\Eloquent\Model;

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
        'origin', 'destination', 'distance'
    ];

    public $timestamps = true;
}
