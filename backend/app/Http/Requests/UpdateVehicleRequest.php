<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'capacity_kg' => 'sometimes|numeric|min:0',
            'max_stops' => 'sometimes|integer|min:0',
            'available' => 'sometimes|boolean',
        ];
    }
}
