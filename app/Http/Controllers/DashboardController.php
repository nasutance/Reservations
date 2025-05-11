<?php

namespace App\Http\Controllers;

use App\Models\{Reservation, User, Show, Price, Artist, Type, ArtistType, Representation, Location};
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord personnalisé selon le rôle de l'utilisateur connecté.
     * - L'administrateur voit tout (accès global).
     * - Le membre voit uniquement ses propres réservations.
     */
    public function index()
    {
        // Récupère l'utilisateur authentifié avec ses rôles
        $user = Auth::user()->load('roles');

        // Vérifie si l'utilisateur a le droit de voir toutes les réservations
        // → typiquement un administrateur
        if ($user->can('viewAny', Reservation::class)) {
            return Inertia::render('Dashboard/Dashboard', [
                // Toutes les réservations avec leur représentation, spectacle, lieu, et utilisateur liés
                'reservations' => Reservation::with(['representations.show', 'representations.location', 'user'])->get(),

                // Tous les utilisateurs avec leurs rôles
                'users' => User::with('roles')->get(),

                // Liste complète des spectacles
                'shows' => Show::all(),

                // Représentations avec leurs lieux et spectacles
                'representations' => Representation::with('location','show')->get(),

                // Tous les lieux
                'locations' => Location::all(),

                // Tous les artistes
                'artists' => Artist::all(),

                // Tous les types d'artistes avec les types et spectacles associés
                'artistTypes' => ArtistType::with(['type', 'shows'])->get(),

                // Tous les types (métier)
                'types' => Type::all(),

                // Tous les tarifs
                'prices' => Price::all(),

                // Récupération brute de la table pivot entre prix et spectacles
                'priceShow' => DB::table('price_show')->select('show_id', 'price_id')->get(),

                // Tous les rôles
                'roles' => \App\Models\Role::all(),

                // Indicateur de rôle administrateur (peut être utilisé en front)
                'isAdmin' => true,
            ]);
        }

        // Si l'utilisateur n’est pas admin → affiche uniquement ses réservations
        return Inertia::render('Dashboard/Dashboard', [
            'reservations' => $user->reservations()->with(['representations.show'])->get(),
            'prices' => Price::all(),
            'isAdmin' => false,
        ]);
    }
}
