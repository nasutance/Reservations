<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/**
 * AppServiceProvider est chargé d'enregistrer et de configurer les services globaux de l'application.
 * Il s'agit d'un point central pour initialiser des comportements transversaux.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Méthode appelée au démarrage pour enregistrer les services.
     * Utilisée pour l'injection de dépendances ou les bindings dans le conteneur.
     * Ici, rien n'est nécessaire.
     */
    public function register() {}

    /**
     * Méthode appelée après l’enregistrement de tous les services.
     * C’est ici qu’on peut définir des comportements de démarrage spécifiques.
     */
    public function boot()
    {
        // 🔒 Environnement sécurisé :
        // Si l'application est en production, on force toutes les URL à utiliser HTTPS
        // Cela évite que les liens générés soient en HTTP non sécurisé.
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // 🔁 Chargement manuel du fichier de routes API (optionnel)
        // Normalement géré automatiquement par Laravel, mais on peut l'ajouter ici pour s'assurer qu'il est bien inclus.
        Route::middleware('api')            // On applique le middleware "api"
            ->prefix('api')                 // Préfixe de l'URL pour toutes les routes concernées
            ->group(base_path('routes/api.php')); // On charge les routes du fichier api.php
    }
}
