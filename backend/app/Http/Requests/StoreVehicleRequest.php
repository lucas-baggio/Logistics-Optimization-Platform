<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'capacity_kg' => 'required|numeric|min:0',
            'max_stops' => 'required|integer|min:0',
            'available' => 'sometimes|boolean',
        ];
    }
}
