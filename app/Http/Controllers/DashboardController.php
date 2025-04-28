<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Show;
use App\Models\Price;
use App\Models\Role;
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
            $shows = Show::with('representations.location')->select('id', 'title', 'description', 'duration', 'bookable')->get();
            $users = User::with('roles')->select('id', 'firstname', 'lastname', 'email','langue')->get();

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
