<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
