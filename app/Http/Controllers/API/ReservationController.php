<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Representation;
use App\Models\Price;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends Controller
{
    use AuthorizesRequests; // Active le système de contrôle d’accès basé sur les policies

    /**
     * Affiche toutes les réservations visibles par l’utilisateur connecté
     * (GET /api/reservations)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Si l'utilisateur peut voir toutes les réservations (admin, producteur, etc.)
        if ($user->can('viewAny', Reservation::class)) {
            $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
        } else {
            // Sinon, il ne peut voir que les siennes
            $query = $user->reservations()->with(['representations.show']);
        }

        // Filtrage facultatif par statut
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Affiche une réservation spécifique avec ses représentations
     * et liens vers celles-ci (GET /api/reservations/{id})
     */
    public function show(Request $request, $id)
    {
        // Chargement de la réservation avec les relations nécessaires
        $reservation = Reservation::with('representations.show')->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        $this->authorize('view', $reservation); // Vérification d’accès

        // Ajoute le prix total si demandé avec ?include=total_price
        if ($request->query('include') === 'total_price') {
            $totalPrice = $reservation->representations->sum(function ($representation) {
                return $representation->pivot->quantity * Price::where('id', $representation->pivot->price_id)->value('price');
            });
            $reservation->total_price = number_format($totalPrice, 2, '.', '');
        }

        // Ajout des liens HATEOAS vers les représentations liées
        $reservation->reservations_links = $reservation->representations->map(function ($representation) {
            return [
                'id' => $representation->id,
                'link' => route('reservations.show', ['reservation' => $representation->pivot->reservation_id]),
            ];
        });

        return response()->json($reservation);
    }

    /**
     * Crée une réservation pour une représentation donnée (POST /api/reservations)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class); // Vérification des droits

        // Validation des données entrantes
        $validated = $request->validate([
            'representation_id' => 'required|exists:representations,id',
            'price_id' => 'required|exists:prices,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Vérification que la représentation existe
        $representation = Representation::find($validated['representation_id']);
        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée.'], 404);
        }

        // Vérifie si le prix choisi correspond bien au spectacle concerné
        $show = $representation->show;
        $validPriceForShow = $show->prices()->where('id', $validated['price_id'])->exists();
        $isSpecialPrice = Price::find($validated['price_id'])->is_special ?? false;

        // Si le prix n’est pas lié au spectacle ni spécial, on refuse
        if (!$validPriceForShow && !$isSpecialPrice) {
            return response()->json(['message' => 'Ce prix n’est pas valide pour ce spectacle.'], 400);
        }

        // Création de la réservation
        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'status' => 'en attente',
        ]);

        // Association avec la représentation (pivot : quantity, price_id)
        $reservation->representations()->attach($validated['representation_id'], [
            'price_id' => $validated['price_id'],
            'quantity' => $validated['quantity']
        ]);

        // Retourne la réservation avec les données utiles
        return response()->json($reservation->load('representations.show'), 201);
    }

    /**
     * Met à jour la quantité d’une représentation dans une réservation (PUT /api/reservations/{id})
     */
    public function update(Request $request, $id)
    {
        // Validation des nouvelles données
        $validated = $request->validate([
            'representation_id' => 'required|exists:representations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        $this->authorize('update', $reservation); // Vérifie les droits

        // Vérifie que la représentation est bien liée à cette réservation
        if (!$reservation->representations()->where('representation_id', $validated['representation_id'])->exists()) {
            return response()->json(['message' => 'Cette représentation n’est pas liée à cette réservation.'], 400);
        }

        // Mise à jour du pivot (quantité uniquement)
        $reservation->representations()->updateExistingPivot($validated['representation_id'], [
            'quantity' => $validated['quantity']
        ]);

        // Renvoie la réservation avec les données du pivot mises à jour
        return response()->json($reservation->load(['representations' => function ($query) {
            $query->withPivot('quantity', 'price_id');
        }]));
    }

    /**
     * Supprime une réservation (DELETE /api/reservations/{id})
     */
    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        $this->authorize('delete', $reservation); // Vérifie si l'utilisateur peut supprimer

        $reservation->delete();

        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }
}
