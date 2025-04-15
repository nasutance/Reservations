<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Representation;
use App\Models\Show;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function create(Show $show)
    {
        // On charge les représentations et leurs prix pour la vue VueJS
        $show->load(['representations.location', 'prices']);
        return Inertia::render('Reservation/Reserve', [
            'show' => $show
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class);

        $validated = $request->validate([
            'representation_id' => 'required|exists:representations,id',
            'price_id' => 'required|exists:prices,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $representation = Representation::find($validated['representation_id']);

        if (!$representation) {
            return redirect()->back()->withErrors(['representation_id' => 'Représentation introuvable.']);
        }

        $show = $representation->show;
        $validPrice = $show->prices()->where('id', $validated['price_id'])->exists();
        $isSpecial = Price::find($validated['price_id'])->is_special ?? false;

        if (!$validPrice && !$isSpecial) {
            return redirect()->back()->withErrors(['price_id' => 'Ce tarif n’est pas disponible pour ce spectacle.']);
        }

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'status' => 'en attente',
        ]);

        $reservation->representations()->attach($validated['representation_id'], [
            'price_id' => $validated['price_id'],
            'quantity' => $validated['quantity']
        ]);

        return redirect()->route('dashboard')->with('success', 'Réservation enregistrée avec succès !');
    }

    public function index()
    {
        $user = Auth::user();

        $query = $user->reservations()->with(['representations.show']);

        if ($user->can('viewAny', Reservation::class)) {
            $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
        }

        return Inertia::render('Reservation/Index', [
            'reservations' => $query->get()
        ]);
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $reservation->load(['representations.show']);

        return Inertia::render('Reservation/Show', [
            'reservation' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return redirect()->route('dashboard')->with('success', 'Réservation annulée.');
    }
}
