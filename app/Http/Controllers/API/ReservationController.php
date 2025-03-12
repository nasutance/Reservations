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

        // Un admin voit toutes les réservations, un membre voit seulement les siennes
        if (Gate::allows('view-all-reservations')) {
            return response()->json(Reservation::with('representations.show')->get(), 200);
        }

        return response()->json($user->reservations()->with('representations.show')->get(), 200);
    }

    /**
     * Récupérer une seule réservation
     */
    public function show(Request $request, Reservation $reservation)
    {
        if ($request->user()->id !== $reservation->user_id && !Gate::allows('view-all-reservations')) {
            return response()->json(['message' => 'Accès interdit'], 403);
        }

        return response()->json($reservation->load('representations.show'), 200);
    }

    /**
     * Créer une réservation
     */

     public function store(Request $request)
     {
        $validated = $request->validate([
        'representation_id' => 'required|exists:representations,id',
        'price_id' => 'required|exists:prices,id',
        'quantity' => 'required|integer|min:1',
      ]);

      $representation = \App\Models\Representation::find($validated['representation_id']);
      if (!$representation) {
        return response()->json(['message' => 'Représentation non trouvée'], 404);
      }

      $show = $representation->show;

      // Vérifier si le prix est bien associé à ce show via price_show
      $validPriceForShow = \App\Models\PriceShow::where('show_id', $show->id)
                                              ->where('price_id', $validated['price_id'])
                                              ->exists();

      // Vérifier si c'est un prix libre (exception)
      $isSpecialPrice = \App\Models\Price::find($validated['price_id'])->is_special ?? false;

      if (!$validPriceForShow && !$isSpecialPrice) {
        return response()->json(['message' => 'Ce prix n’est pas valide pour ce spectacle.'], 400);
      }

      // Création de la réservation
      $reservation = Reservation::create([
          'user_id' => $request->user()->id,
          'status' => 'en attente',
      ]);

      // Attacher la représentation avec le prix choisi et la quantité
      $reservation->representations()->attach($validated['representation_id'], [
          'price_id' => $validated['price_id'], // On stocke price_id dans la table pivot
        'quantity' => $validated['quantity']
      ]);

      return response()->json($reservation->load('representations'), 201);
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
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $reservation->representations()->updateExistingPivot($reservation->representations->first()->id, [
            'quantity' => $validated['quantity']
        ]);

        return response()->json($reservation->load('representations'), 200);
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
