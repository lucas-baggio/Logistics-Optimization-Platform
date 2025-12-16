<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vehicle_id' => 'required|integer|exists:vehicles,id',
            'status' => 'required|string|max:255',
            'optimized_distance_km' => 'required|numeric|min:0',
            'optimized_duration_min' => 'required|integer|min:0',
            'optimized_sequence_json' => 'required|array',
            'polyline_data' => 'sometimes|nullable|string',
        ];
    }
}
