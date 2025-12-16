<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRouteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vehicle_id' => 'sometimes|integer|exists:vehicles,id',
            'status' => 'sometimes|string|max:255',
            'optimized_distance_km' => 'sometimes|numeric|min:0',
            'optimized_duration_min' => 'sometimes|integer|min:0',
            'optimized_sequence_json' => 'sometimes|array',
            'polyline_data' => 'sometimes|nullable|string',
        ];
    }
}
