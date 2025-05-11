<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use Spatie\Feed\Feed;
use Spatie\Feed\FeedItem;
use App\Models\Representation;

class FeedController extends Controller
{
    /**
     * Génère et retourne le flux RSS des représentations à venir.
     * Utilise une vue fournie par le package Spatie/Feed (feed::rss).
     */
    public function index()
    {
        // Récupère les éléments du flux via une méthode statique définie dans le modèle
        $items = Representation::getFeedItems();

        // Métadonnées du flux RSS
        $meta = [
            'title' => 'Prochaines représentations',     // Titre du flux RSS
            'link' => '/rss',                            // Lien vers le flux
            'description' => 'Liste des représentations à venir.', // Description du contenu
            'image' => '',                                // Image/logo du flux (facultatif)
            'language' => 'fr-BE',                        // Langue du flux selon la norme IETF
            'updated' => now()->toRssString(),            // Date de mise à jour du flux
        ];

        // Retourne une réponse contenant la vue du flux RSS, avec le bon content-type
        return response()
            ->view('feed::rss', compact('items', 'meta'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
