<?php

namespace App\Http\Controllers;

use App\Models\{Reservation, User, Show, Price, Artist, Type, ArtistType, Representation};
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index()
    {

        $user = Auth::user()->load('roles');

        // Si ADMIN → Accès complet
        if ($user->can('viewAny', Reservation::class)) {
            return Inertia::render('Dashboard/Dashboard', [
                'reservations' => Reservation::with(['representations.show', 'representations.location', 'user'])->get(),
                'users' => User::with('roles')->get(),
                'shows' => Show::all(),
                'representations' => Representation::with('location')->get(),
                'artists' => Artist::all(),
                'artistTypes' => ArtistType::with(['type', 'shows'])->get(),
                'types' => Type::all(),
                'prices' => Price::select('id', 'description')->get(),
                'roles' => \App\Models\Role::all(),
                'isAdmin' => true,
            ]);
        }

        // Sinon, membre → Ses réservations uniquement
        return Inertia::render('Dashboard/Dashboard', [
            'reservations' => $user->reservations()->with(['representations.show'])->get(),
            'isAdmin' => false,
        ]);
    }
}
