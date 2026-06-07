<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation sur le modèle est vérifiée dans le contrôleur
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'slug'        => ['sometimes', 'string', 'max:255', "unique:shows,slug,{$id}"],
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'poster_url'  => ['nullable', 'url'],
            'duration'    => ['sometimes', 'integer', 'min:1'],
            'created_in'  => ['sometimes', 'integer', 'min:1800', 'max:' . date('Y')],
            'location_id' => ['sometimes', 'integer', 'exists:locations,id'],
            'bookable'    => ['sometimes', 'boolean'],
        ];
    }
}
