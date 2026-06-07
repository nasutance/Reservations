<?php

namespace App\Http\Requests\Api;

use App\Models\Show;
use Illuminate\Foundation\Http\FormRequest;

class StoreShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Show::class);
    }

    public function rules(): array
    {
        return [
            'slug'        => ['required', 'string', 'max:255', 'unique:shows,slug'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'poster_url'  => ['nullable', 'url'],
            'duration'    => ['required', 'integer', 'min:1'],
            'created_in'  => ['required', 'integer', 'min:1800', 'max:' . date('Y')],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'bookable'    => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique'           => 'Ce slug est déjà utilisé.',
            'title.required'        => 'Le titre est obligatoire.',
            'duration.required'     => 'La durée est obligatoire.',
            'created_in.required'   => 'L\'année de création est obligatoire.',
            'location_id.required'  => 'Le lieu est obligatoire.',
            'location_id.exists'    => 'Ce lieu n\'existe pas.',
            'bookable.required'     => 'Le champ réservable est obligatoire.',
        ];
    }
}
