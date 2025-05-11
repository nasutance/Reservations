<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Détermine les règles de validation applicables à la mise à jour d’un profil utilisateur.
     *
     * Les règles utilisent "sometimes" : chaque champ est facultatif,
     * mais s’il est présent dans la requête, il doit être valide.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // Prénom : texte facultatif, 255 caractères max
            'firstname' => ['sometimes', 'string', 'max:255'],

            // Nom : texte facultatif, 255 caractères max
            'lastname' => ['sometimes', 'string', 'max:255'],

            // Email : facultatif, format email valide, minuscule, unique sauf pour l’utilisateur connecté
            'email' => [
                'sometimes',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id)
            ],

            // Login : facultatif, max 30 caractères, unique sauf pour l’utilisateur actuel
            'login' => [
                'sometimes',
                'string',
                'max:30',
                Rule::unique('users')->ignore($this->user()->id)
            ],

            // Langue : facultatif, doit être dans la liste autorisée
            'langue' => [
                'sometimes',
                'string',
                'in:fr,en,nl,de,it'
            ],
        ];
    }
}
