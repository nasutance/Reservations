<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Autorise la requête sans condition.
     * Peut être modifié pour restreindre l’accès à certains contextes.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Définition des règles de validation pour la requête de connexion.
     * Ces règles sont automatiquement appliquées avant l’appel à `authenticate()`.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],     // E-mail requis et au bon format
            'password' => ['required', 'string'],           // Mot de passe requis
        ];
    }

    /**
     * Tente d’authentifier l’utilisateur avec les identifiants fournis.
     * Lance une exception de validation en cas d’échec.
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited(); // Vérifie le seuil de tentatives

        // Authentifie l’utilisateur avec les credentials (option remember facultative)
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey()); // Incrémente le compteur d’échecs

            // Déclenche une erreur de validation personnalisée sur le champ email
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Réinitialise le compteur si la connexion est réussie
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Empêche l’authentification si trop de tentatives échouées ont été faites.
     * Déclenche un événement de verrouillage et affiche un message d’erreur.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return; // Moins de 5 tentatives : autorisé
        }

        event(new Lockout($this)); // Déclenche l’événement Lockout (utile pour audit/log)

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Clé de limitation de débit basée sur l’e-mail et l’adresse IP de l’utilisateur.
     * Sert à suivre les tentatives échouées dans Redis ou cache.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
