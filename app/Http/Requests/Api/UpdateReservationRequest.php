<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'autorisation sur le modèle est vérifiée dans le contrôleur
        // (nécessite l'instance de Reservation)
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'representation_id' => ['required', 'integer', 'exists:representations,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'representation_id.required' => 'La représentation est obligatoire.',
            'representation_id.exists'   => 'Cette représentation n\'existe pas.',
            'quantity.required'          => 'La quantité est obligatoire.',
            'quantity.min'               => 'La quantité doit être d\'au moins 1.',
        ];
    }
}
