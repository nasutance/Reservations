<?php

namespace App\Http\Requests\Api;

use App\Models\Representation;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepresentationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Representation::class);
    }

    public function rules(): array
    {
        return [
            'schedule'    => ['required', 'date'],
            'show_id'     => ['required', 'integer', 'exists:shows,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule.required'    => 'La date de représentation est obligatoire.',
            'schedule.date'        => 'La date de représentation est invalide.',
            'show_id.required'     => 'Le spectacle est obligatoire.',
            'show_id.exists'       => 'Ce spectacle n\'existe pas.',
            'location_id.required' => 'Le lieu est obligatoire.',
            'location_id.exists'   => 'Ce lieu n\'existe pas.',
        ];
    }
}
