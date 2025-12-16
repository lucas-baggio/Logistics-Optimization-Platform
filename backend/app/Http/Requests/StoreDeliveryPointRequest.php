<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryPointRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'address' => 'required|string|max:1024',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'weight_kg' => 'required|numeric|min:0',
            'time_window_start' => 'required|date_format:H:i',
            'time_window_end' => 'required|date_format:H:i|after_or_equal:time_window_start',
            'route_id' => 'sometimes|nullable|integer|exists:routes,id',
        ];
    }
}
