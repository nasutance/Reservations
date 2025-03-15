<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Representation;
use App\Models\Price;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class ReservationController extends Controller
{
    /**
     * Lister les réservations de l'utilisateur authentifié
     */
     public function index(Request $request)
   {
       $query = $request->user()->reservations()->with(['representations.show']);

       // Vérifier si l'utilisateur est admin via les Gates
       if (Gate::allows('view-all-reservations')) {
           $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
       }

       // Filtrer par statut (`?status=Payée`)
       if ($request->has('status')) {
           $query->where('status', $request->query('status'));
       }

       return response()->json($query->get(), 200);
   }
      /**
     * Voir une réservation spécifique de l'utilisateur
     */
     public function show(Request $request, $id)
 {
     $reservation = Reservation::with('representations.show')->find($id);

     if (!$reservation) {
         return response()->json(['message' => 'Réservation introuvable.'], 404);
     }

     // Vérifier si l'utilisateur a le droit de voir cette réservation (admin ou propriétaire)
     if ($request->user()->id !== $reservation->user_id && !Gate::allows('view-all-reservations')) {
         return response()->json(['message' => 'Accès interdit.'], 403);
     }

     // Si `?include=total_price` est dans l'URL, on calcule le total
     if ($request->query('include') === 'total_price') {
     $totalPrice = $reservation->representations->sum(function ($representation) {
         return $representation->pivot->quantity * Price::where('id', $representation->pivot->price_id)->value('price');
     });

     $reservation->total_price = number_format($totalPrice, 2, '.', '');
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

        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        // Vérifier l'autorisation avec la Gate
        if (!Gate::allows('update-reservation', $reservation)) {
            return response()->json(['message' => 'Modification interdite.'], 403);
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

      if (!$reservation) {
          return response()->json(['message' => 'Réservation introuvable.'], 404);
      }

      // Vérifier l'autorisation avec la Gate
      if (!Gate::allows('delete-reservation', $reservation)) {
          return response()->json(['message' => 'Annulation interdite.'], 403);
      }

      $reservation->delete();
      return response()->json(['message' => 'Réservation annulée avec succès.']);
    }
}
