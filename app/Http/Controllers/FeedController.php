<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Response;
use Spatie\Feed\Feed;
use Spatie\Feed\FeedItem;
use App\Models\Representation;

class FeedController extends Controller
{
    public function index()
    {
        $items = Representation::getFeedItems();
    
        $meta = [
            'title' => 'Prochaines représentations',
            'link' => '/rss',
            'description' => 'Liste des représentations à venir.',
            'image' => '',
            'language' => 'fr-BE', // ✅ Ajoute cette ligne
            'updated' => now()->toRssString(), // facultatif mais conseillé
        ];
        
    
        return response()->view('feed::rss', compact('items', 'meta'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
