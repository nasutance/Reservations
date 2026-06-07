<?php

namespace App\Http\Requests\Api;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Reservation::class);
    }

    public function rules(): array
    {
        return [
            'representation_id' => ['required', 'integer', 'exists:representations,id'],
            'price_id'          => ['required', 'integer', 'exists:prices,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'representation_id.required' => 'La représentation est obligatoire.',
            'representation_id.exists'   => 'Cette représentation n\'existe pas.',
            'price_id.required'          => 'Le tarif est obligatoire.',
            'price_id.exists'            => 'Ce tarif n\'existe pas.',
            'quantity.required'          => 'La quantité est obligatoire.',
            'quantity.min'               => 'La quantité doit être d\'au moins 1.',
        ];
    }
}
