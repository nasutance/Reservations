<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Vue Blade principale utilisée par Inertia pour toutes les pages.
     * Elle doit contenir le point d’entrée de l’app Vue.js (ex : <div id="app"></div>)
     *
     * Ex. : resources/views/app.blade.php
     */
    protected $rootView = 'app';

    /**
     * Version des assets pour invalider le cache automatiquement.
     * Peut être utilisée pour déclencher un rechargement du front après un déploiement.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request); // Peut être surchargé pour une version personnalisée
    }

    /**
     * Propriétés globales disponibles côté client (Vue.js) dans toutes les pages.
     *
     * Exemple :
     *   - Authentification utilisateur
     *   - Jeton CSRF
     *   - Paramètres globaux, préférences, traductions, etc.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Données d’authentification (utilisateur et ses rôles)
            'auth' => [
                'user' => $request->user() ? $request->user()->load('roles') : null,
            ],

            // Jeton CSRF exposé au frontend (nécessaire pour les requêtes POST sécurisées)
            'csrf_token' => csrf_token(),
        ]);
    }
}
