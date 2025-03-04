<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Gate;

class ReservationController extends Controller
{
    /**
     * Récupérer toutes les réservations de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Un admin peut voir toutes les réservations, un membre voit uniquement les siennes
        if (Gate::allows('view-all-reservations')) {
            return response()->json(Reservation::all(), 200);
        }

        return response()->json($user->reservations, 200);
    }

    /**
    * Récupérer une réservation spécifique
    */
    public function show(Request $request, Reservation $reservation)
    {
      if ($request->user()->id !== $reservation->user_id && !Gate::allows('view-all-reservations')) {
          return response()->json(['message' => 'Accès interdit'], 403);
      }

      return response()->json($reservation, 200);
    }


    /**
     * Créer une nouvelle réservation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
          'representation_id' => 'required|exists:representations,id',
          'quantity' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::create([
          'user_id' => $request->user()->id,
          'status' => 'en attente',
        ]);

        return response()->json($reservation, 201);
    }

    /**
     * Modifier une réservation (seulement par l'utilisateur qui l'a créée)
     */
    public function update(Request $request, Reservation $reservation)
    {
        if ($request->user()->id !== $reservation->user_id) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $validated = $request->validate([
            'date' => 'sometimes|date|after_or_equal:today',
        ]);

        $reservation->update($validated);

        return response()->json($reservation, 200);
    }

    /**
     * Supprimer une réservation (seulement par l'utilisateur qui l'a créée ou un admin)
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        if ($request->user()->id !== $reservation->user_id && !Gate::allows('delete-any-reservation')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        $reservation->delete();

        return response()->json(['message' => 'Réservation supprimée'], 200);
    }
}
