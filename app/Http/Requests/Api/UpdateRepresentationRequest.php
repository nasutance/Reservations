<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRepresentationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'schedule'    => ['sometimes', 'date'],
            'show_id'     => ['sometimes', 'integer', 'exists:shows,id'],
            'location_id' => ['sometimes', 'integer', 'exists:locations,id'],
        ];
    }
}
