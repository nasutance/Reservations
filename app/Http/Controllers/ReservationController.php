<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Representation;
use App\Models\Show;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends Controller
{
  use AuthorizesRequests;

  public function create(Show $show)
  {
    // On charge les représentations et leurs prix pour la vue VueJS

    $show->load(['representations.location', 'prices']);

    return Inertia::render('Reservation/Reserve', [
        'show' => $show,
        'paypalClientId' => config('services.paypal.client_id'),
    ]);
}


public function store(Request $request)
{
  $this->authorize('create', Reservation::class);

  $validated = $request->validate([
      'representation_id' => 'required|exists:representations,id',
      'quantities' => 'required|array|min:1',
      'quantities.*.price_id' => 'required|exists:prices,id',
      'quantities.*.quantity' => 'required|integer|min:1',
      'delivery_method' => 'nullable|string',
      'payment_method' => 'nullable|string',
  ]);

  $representation = Representation::find($validated['representation_id']);

  if (!$representation) {
      return redirect()->back()->withErrors(['representation_id' => 'Représentation introuvable.']);
  }

  $show = $representation->show;

  // Vérifie que tous les price_id sont bien autorisés pour ce spectacle
  foreach ($validated['quantities'] as $line) {
      $allowed = $show->prices()->where('id', $line['price_id'])->exists();
      if (!$allowed) {
          return redirect()->back()->withErrors([
              'quantities' => "Le tarif sélectionné (#{$line['price_id']}) n'est pas valable pour ce spectacle."
          ]);
      }
  }

  $reservation = Reservation::create([
      'user_id' => auth()->id(),
      'status' => 'en attente',
  ]);

  // Ajoute chaque ligne tarifaire dans la table pivot
  foreach ($validated['quantities'] as $line) {
      $reservation->representations()->attach($validated['representation_id'], [
          'price_id' => $line['price_id'],
          'quantity' => $line['quantity']
      ]);
  }

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
