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

    /**
     * Affiche la page de réservation d’un spectacle donné.
     * Charge les représentations et prix liés au spectacle, ainsi que l'identifiant PayPal public.
     */
    public function create(Show $show)
    {
        $show->load(['representations.location', 'prices']); // Eager load

        return Inertia::render('Reservation/Reserve', [
            'show' => $show,
            'paypalClientId' => config('services.paypal.client_id'), // Injecte la clé PayPal pour le frontend
        ]);
    }

    /**
     * Enregistre une nouvelle réservation avec les lignes (quantités, prix) choisies.
     * Vérifie la validité des prix en base et crée les enregistrements dans la table pivot.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class);

        $validated = $request->validate([
            'representation_id' => 'required|exists:representations,id',
            'quantities' => 'required|array|min:1',
            'quantities.*.price_id' => 'required|exists:prices,id',
            'quantities.*.quantity' => 'required|integer|min:0',
            'delivery_method' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        $representation = Representation::find($validated['representation_id']);
        if (!$representation) {
            return redirect()->back()->withErrors(['representation_id' => 'Représentation introuvable.']);
        }

        $show = $representation->show;

        // Vérifie si tous les tarifs sont bien valides pour le spectacle
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

        // Création des lignes associées dans la table pivot
        foreach ($validated['quantities'] as $line) {
            $reservation->representations()->attach($validated['representation_id'], [
                'price_id' => $line['price_id'],
                'quantity' => $line['quantity'] ?? 0,
            ]);
        }

        return response()->json([
            'lastInsertedId' => $reservation->id,
        ]);
    }

    /**
     * Liste les réservations visibles par l'utilisateur connecté.
     * Si admin : accès à toutes les réservations avec infos utilisateur.
     * Sinon : accès uniquement aux siennes.
     */
    public function index()
    {
        $user = Auth::user();

        $query = $user->can('viewAny', Reservation::class)
            ? Reservation::with(['representations.show', 'user:id,firstname,lastname,email'])
            : $user->reservations()->with(['representations.show']);

        return Inertia::render('Dashboard/Dashboard', [
            'reservations' => $query->get()
        ]);
    }

    /**
     * Affiche les détails d'une réservation sélectionnée.
     */
    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);

        $reservation->load(['representations.show']);

        return Inertia::render('Dashboard/Dashboard', [
            'highlightedReservation' => $reservation,
        ]);
    }

    /**
     * Supprime une réservation (logiquement).
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return Inertia::location('/dashboard');
    }

    /**
     * Met à jour une réservation :
     * - Admin : peut changer statut, prix, quantités
     * - Utilisateur : peut uniquement modifier le statut de sa réservation
     */
    public function update(Request $request, Reservation $reservation)
    {
        $user = Auth::user();

        // ADMIN : modification complète autorisée
        if ($user->hasRole('admin')) {
            $validated = $request->validate([
                'status' => 'required|string|in:en attente,payée,annulée',
            ]);

            $reservation->update(['status' => $validated['status']]);

            if (isset($request->representations)) {
                foreach ($request->representations as $rep) {
                    $current = DB::table('representation_reservation')
                        ->where('reservation_id', $reservation->id)
                        ->where('price_id', $rep['original_price_id'] ?? $rep['price_id'])
                        ->where('quantity', $rep['original_quantity'] ?? $rep['quantity'])
                        ->first();

                    if (!$current) continue;

                    // Mise à jour conditionnelle
                    if ($rep['quantity'] != $current->quantity) {
                        DB::table('representation_reservation')
                            ->where('reservation_id', $reservation->id)
                            ->where('price_id', $current->price_id)
                            ->where('quantity', $current->quantity)
                            ->update(['quantity' => $rep['quantity']]);
                    }

                    if ($rep['price_id'] != $current->price_id) {
                        DB::table('representation_reservation')
                            ->where('reservation_id', $reservation->id)
                            ->where('price_id', $current->price_id)
                            ->where('quantity', $current->quantity)
                            ->update(['price_id' => $rep['price_id']]);
                    }
                }
            }

            return Inertia::location('/dashboard');
        }

        // UTILISATEUR : peut seulement modifier le statut si propriétaire
        if ($user->id !== $reservation->user_id) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:en attente,payée,annulée',
        ]);

        $reservation->update(['status' => $validated['status']]);

        session()->flash('reservationId', $reservation->id);

        return response()->json([
            'redirect' => route('reservation.thanks'),
        ]);
        

        
    }

    /**
     * Restaure une réservation supprimée (soft delete).
     */
    public function restore($id)
    {
        $reservation = Reservation::withTrashed()->findOrFail($id);
        $reservation->restore();

        return Inertia::location('/dashboard');
    }

    /**
     * Ajoute une ligne tarifaire vide (quantité 0) à une réservation.
     */
    public function addLine(Request $request, Reservation $reservation)
    {
        $request->validate([
            'price_id' => 'required|exists:prices,id',
        ]);

        $user = Auth::user();

        if (!$user->hasRole('admin') && $user->id !== $reservation->user_id) {
            abort(403);
        }

        $representationId = $reservation->representations->first()->id ?? null;

        if (!$representationId) {
            return redirect()->back()->withErrors(['representation_id' => 'Impossible de trouver une représentation pour cette réservation.']);
        }

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

        DB::table('representation_reservation')->insert([
            'reservation_id' => $reservation->id,
            'representation_id' => $representationId,
            'price_id' => $request->price_id,
            'quantity' => 0,
        ]);

        return redirect()->back();
    }

    /**
     * Supprime une ligne tarifaire (une combinaison représentation/prix) d’une réservation.
     */
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
