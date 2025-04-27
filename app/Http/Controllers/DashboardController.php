<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Show;
use App\Models\Price;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $connectedUser = Auth::user()->load('roles');
        $users = [];
        $shows = [];

        $query = $connectedUser->reservations()->with(['representations.show']);

        if ($connectedUser->can('viewAny', Reservation::class)) {
            $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
            $shows = Show::all();
            $users = User::select('id', 'firstname', 'lastname', 'email')->get();
        }

        return Inertia::render('Dashboard/Dashboard', [
            'reservations' => $query->get(),
            'users' => $users,
            'shows' => $shows,
            'isAdmin' => $connectedUser->hasRole('admin'),
            'prices' => Price::select('id', 'description')->get(),
        ]);
    }
}
