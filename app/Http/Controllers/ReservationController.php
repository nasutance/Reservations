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
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function create(Show $show)
    {
        // Charge les représentations et leurs prix pour la vue VueJS
        $show->load(['representations.location', 'prices']);

        return Inertia::render('Reservation/Reserve', [
            'show' => $show,
            'paypalClientId' => config('services.paypal.client_id'),
        ]);
    }

    public function store(Request $request)
{
    $this->authorize('create', Reservation::class);

    // Validation des données envoyées par le frontend
    $validated = $request->validate([
        'representation_id' => 'required|exists:representations,id',
        'quantities' => 'required|array|min:1',
        'quantities.*.price_id' => 'required|exists:prices,id',
        'quantities.*.quantity' => 'required|integer|min:0', // Permet de gérer les quantités égales à 0
        'delivery_method' => 'nullable|string',
        'payment_method' => 'nullable|string',
    ]);

    // Récupérer la représentation (pour vérifier la validité du tarif)
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

    // Création de la réservation
    $reservation = Reservation::create([
        'user_id' => auth()->id(),
        'status' => 'en attente',
    ]);

    // Ajout des lignes dans la table pivot `representation_reservation`
    foreach ($validated['quantities'] as $line) {
        // Si la quantité est nulle ou manquante, on la force à 0
        $quantity = $line['quantity'] ?? 0;

        // Utilisation de attach() avec la quantité forcée à 0 si nécessaire
        $reservation->representations()->attach($validated['representation_id'], [
            'price_id' => $line['price_id'],
            'quantity' => $quantity,  // On assure que quantity est 0 si elle est nulle
        ]);
    }

    return response()->json([
        'lastInsertedId' => $reservation->id,
    ]);
}


    public function index()
    {
        $user = Auth::user();

        $query = $user->reservations()->with(['representations.show']);

        if ($user->can('viewAny', Reservation::class)) {
            $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
        } else {
            $query = $user->reservations()->with(['representations.show']);
        }
        

        return Inertia::render('Dashboard/Dashboard', [
            'reservations' => $query->get()
        ]);
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
    
        $reservation->load(['representations.show']);
    
        return Inertia::render('Dashboard/Dashboard', [
            'highlightedReservation' => $reservation,
        ]);
    }
    

    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        // Suppression de la réservation (suppression logique)
        $reservation->delete();

        // Retourner la réponse avec un message de succès
        return Inertia::location('/dashboard');

    }

    public function update(Request $request, Reservation $reservation)
        {
            // Vérifie si l'utilisateur est un admin ou l'auteur de la réservation
            $user = Auth::user();

            // Si l'utilisateur est un admin, il peut mettre à jour toutes les réservations
            if ($user->hasRole('admin')) {
                // Validation des données pour l'admin
                $validated = $request->validate([
                    'status' => 'required|string|in:en attente,payée,annulée',
                ]);

                // Mise à jour du statut de la réservation
                $reservation->update([
                    'status' => $validated['status'],
                ]);

                // Si l'admin met à jour des détails supplémentaires (quantités ou prix)
                if (isset($request->representations)) {
                    foreach ($request->representations as $rep) {
                        $newPriceId = $rep['price_id'];
                        $newQuantity = $rep['quantity'];

                        // Étape 1 : récupérer l'entrée actuelle basée sur reservation_id + price_id
                        $current = DB::table('representation_reservation')
                            ->where('reservation_id', $reservation->id)
                            ->where('price_id', $rep['original_price_id'] ?? $newPriceId) // On suppose que le frontend envoie l'ancien price_id
                            ->where('quantity', $rep['original_quantity'] ?? $newQuantity) // On suppose aussi l'ancien quantity
                            ->first();

                        if (!$current) continue;

                        // Cas 1 : quantity modifiée → on garde price_id comme référence
                        if ($newQuantity != $current->quantity) {
                            DB::table('representation_reservation')
                                ->where('reservation_id', $reservation->id)
                                ->where('price_id', $current->price_id)
                                ->where('quantity', $current->quantity)
                                ->update([
                                    'quantity' => $newQuantity
                                ]);
                        }

                        // Cas 2 : price_id modifié → on garde quantity comme référence
                        if ($newPriceId != $current->price_id) {
                            DB::table('representation_reservation')
                                ->where('reservation_id', $reservation->id)
                                ->where('price_id', $current->price_id)
                                ->where('quantity', $current->quantity)
                                ->update([
                                    'price_id' => $newPriceId
                                ]);
                        }
                    }
                }


                // Si l'admin met à jour, rediriger vers le dashboard admin avec un message de succès
                return Inertia::location('/dashboard');
            }

            // Si l'utilisateur n'est pas un admin, il peut uniquement modifier ses propres réservations
            if ($user->id !== $reservation->user_id) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Pour les utilisateurs (non-admin), on met à jour uniquement leur propre réservation
            $validated = $request->validate([
                'status' => 'required|string|in:en attente,payée,annulée',
            ]);

            $reservation->update([
                'status' => $validated['status'],
            ]);

            session()->flash('reservationId', $reservation->id);
            return to_route('dashboard')->with([
                'success' => 'Réservation mise à jour.',
                'reservationId' => $reservation->id
            ]);


        }

        public function restore($id)
    {
        // Trouver la réservation annulée
        $reservation = Reservation::withTrashed()->findOrFail($id);

        // Restaurer la réservation
        $reservation->restore();

        // Retourner vers le dashboard avec un message de succès
        return Inertia::location('/dashboard');

    }

    public function addLine(Request $request, Reservation $reservation)
    {
        $request->validate([
            'price_id' => 'required|exists:prices,id',
        ]);

        $user = Auth::user();

        if (!$user->hasRole('admin') && $user->id !== $reservation->user_id) {
            abort(403);
        }

        // 🧠 Récupérer le representation_id depuis la relation
        $representationId = $reservation->representations->first()->id ?? null;

        if (!$representationId) {
            return redirect()->back()->withErrors(['representation_id' => 'Impossible de trouver une représentation pour cette réservation.']);
        }

        // Vérifie si cette combinaison existe déjà
        $exists = DB::table('representation_reservation')
            ->where('reservation_id', $reservation->id)
            ->where('representation_id', $representationId)
            ->where('price_id', $request->price_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors([
                'price_id' => 'Ce tarif est déjà utilisé pour cette réservation.',
            ]);
        }

        // ✅ Insertion complète avec representation_id
        DB::table('representation_reservation')->insert([
            'reservation_id' => $reservation->id,
            'representation_id' => $representationId,
            'price_id' => $request->price_id,
            'quantity' => 0,
        ]);

        return redirect()->back();

    }


    public function destroyLine(Reservation $reservation, Representation $representation, $priceId)
    {
        DB::table('representation_reservation')
            ->where('reservation_id', $reservation->id)
            ->where('representation_id', $representation->id)
            ->where('price_id', $priceId)
            ->delete();
    
        return back();
    }
    

}
