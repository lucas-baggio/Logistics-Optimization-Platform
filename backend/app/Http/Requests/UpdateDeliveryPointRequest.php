<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryPointRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'address' => 'sometimes|string|max:1024',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'weight_kg' => 'sometimes|numeric|min:0',
            'time_window_start' => 'sometimes|date_format:H:i',
            'time_window_end' => 'sometimes|date_format:H:i|after_or_equal:time_window_start',
            'route_id' => 'sometimes|nullable|integer|exists:routes,id',
            'is_routed' => 'sometimes|boolean',
        ];
    }
}
