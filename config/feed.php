<?php

// Ce fichier retourne la configuration du flux RSS via le package Spatie/laravel-feed
return [
    'feeds' => [
        'main' => [
            /*
             * Définition de la source de données pour le flux RSS.
             * On précise ici la classe Eloquent et la méthode statique qui retourne
             * la collection d'éléments à inclure dans le flux.
             */
            'items' => [App\Models\Representation::class, 'getFeedItems'],

            /*
             * URL publique à laquelle le flux RSS sera disponible.
             * Exemple : https://monsite.com/rss
             */
            'url' => '/rss',

            /*
             * Métadonnées du flux : titre, description et langue du contenu.
             * Cela s'affiche dans les lecteurs RSS.
             */
            'title' => 'Prochaines représentations',
            'description' => 'Liste des représentations à venir',
            'language' => 'fr-BE', // Langue utilisée dans le flux

            /*
             * Image associée au flux. Peut être utilisée comme logo ou icône.
             * Laisser vide si aucune image ne doit être associée.
             */
            'image' => '',

            /*
             * Format du flux : 'rss', 'atom' ou 'json'.
             * Ici, on choisit le format RSS 2.0.
             */
            'format' => 'rss',

            /*
             * Vue Blade utilisée pour afficher le contenu du flux.
             * La vue doit respecter la structure du format choisi.
             */
            'view' => 'feed::rss', 

            /*
             * Type MIME du lien dans les balises <link> HTML.
             * Vide = automatique selon le format.
             */
            'type' => '',

            /*
             * Type de contenu HTTP renvoyé dans la réponse.
             * Vide = déterminé automatiquement par le package.
             */
            'contentType' => '',
        ],
    ],
];
