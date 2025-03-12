<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Representation;
use App\Models\Price;

class ReservationController extends Controller
{
    /**
     * Lister les réservations de l'utilisateur authentifié
     */
    public function index(Request $request)
    {
        $reservations = $request->user()->reservations()->with(['representations', 'representations.show'])->get();
        return response()->json($reservations);
    }

    /**
     * Voir une réservation spécifique de l'utilisateur
     */
    public function show(Request $request, $id)
    {
        $reservation = $request->user()->reservations()->with(['representations', 'representations.show'])->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        return response()->json($reservation);
    }

    /**
     * Créer une nouvelle réservation pour l'utilisateur
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'representation_id' => 'required|exists:representations,id',
            'price_id' => 'required|exists:prices,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $representation = Representation::find($validated['representation_id']);
        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée.'], 404);
        }

        $show = $representation->show;
        $validPriceForShow = $show->prices()->where('id', $validated['price_id'])->exists();
        $isSpecialPrice = Price::find($validated['price_id'])->is_special ?? false;

        if (!$validPriceForShow && !$isSpecialPrice) {
            return response()->json(['message' => 'Ce prix n’est pas valide pour ce spectacle.'], 400);
        }

        // Création de la réservation
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'status' => 'en attente',
        ]);

        // Lier la représentation et le prix choisi
        $reservation->representations()->attach($validated['representation_id'], [
            'price_id' => $validated['price_id'],
            'quantity' => $validated['quantity']
        ]);

        return response()->json($reservation->load('representations.show'), 201);
    }

    /**
     * Modifier une réservation existante (ex: changer la quantité)
     */
     public function update(Request $request, $id)
     {
         $validated = $request->validate([
             'representation_id' => 'required|exists:representations,id',
             'quantity' => 'required|integer|min:1',
         ]);

         $reservation = $request->user()->reservations()->find($id);
         if (!$reservation) {
             return response()->json(['message' => 'Réservation introuvable.'], 404);
         }

         // Vérifier si la représentation est bien liée à cette réservation
         if (!$reservation->representations()->where('representation_id', $validated['representation_id'])->exists()) {
             return response()->json(['message' => 'Cette représentation n’est pas liée à cette réservation.'], 400);
         }

         // Mettre à jour la quantité dans la table pivot `representation_reservation`
         $reservation->representations()->updateExistingPivot($validated['representation_id'], [
             'quantity' => $validated['quantity']
         ]);

         return response()->json($reservation->load(['representations' => function ($query) {
             $query->withPivot('quantity', 'price_id');
         }]));
     }

    /**
     * Annuler une réservation (soft delete)
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation || $reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Réservation introuvable ou non autorisée.'], 404);
        }

        $reservation->delete();
        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }
}
