<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes','string','lowercase','email','max:255',Rule::unique(User::class)->ignore($this->user()->id)],
            'login' => ['sometimes', 'string', 'max:30', Rule::unique('users')->ignore($this->user()->id)],
            'langue' => ['sometimes', 'string', 'in:fr,en,nl,de,it'],
        ];
    }
}
