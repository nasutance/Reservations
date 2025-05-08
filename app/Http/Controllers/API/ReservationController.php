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
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->can('viewAny', Reservation::class)) {
            $query = Reservation::with(['representations.show', 'user:id,firstname,lastname,email']);
        } else {
            $query = $user->reservations()->with(['representations.show']);
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        return response()->json($query->get(), 200);
    }

    public function show(Request $request, $id)
    {
        $reservation = Reservation::with('representations.show')->find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        $this->authorize('view', $reservation);

        if ($request->query('include') === 'total_price') {
            $totalPrice = $reservation->representations->sum(function ($representation) {
                return $representation->pivot->quantity * Price::where('id', $representation->pivot->price_id)->value('price');
            });
            $reservation->total_price = number_format($totalPrice, 2, '.', '');
        }

        // 🔁 Réintégration du champ reservations_links
        $reservation->reservations_links = $reservation->representations->map(function ($representation) {
            return [
                'id' => $representation->id,
                'link' => route('reservations.show', ['reservation' => $representation->pivot->reservation_id]),
            ];
        });

        return response()->json($reservation);
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
            return response()->json(['message' => 'Représentation non trouvée.'], 404);
        }

        $show = $representation->show;
        $validPriceForShow = $show->prices()->where('id', $validated['price_id'])->exists();
        $isSpecialPrice = Price::find($validated['price_id'])->is_special ?? false;

        if (!$validPriceForShow && !$isSpecialPrice) {
            return response()->json(['message' => 'Ce prix n’est pas valide pour ce spectacle.'], 400);
        }

        $reservation = Reservation::create([
            'user_id' => $request->user()->id,
            'status' => 'en attente',
        ]);

        $reservation->representations()->attach($validated['representation_id'], [
            'price_id' => $validated['price_id'],
            'quantity' => $validated['quantity']
        ]);

        return response()->json($reservation->load('representations.show'), 201);
    }

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

        $this->authorize('update', $reservation);

        if (!$reservation->representations()->where('representation_id', $validated['representation_id'])->exists()) {
            return response()->json(['message' => 'Cette représentation n’est pas liée à cette réservation.'], 400);
        }

        $reservation->representations()->updateExistingPivot($validated['representation_id'], [
            'quantity' => $validated['quantity']
        ]);

        return response()->json($reservation->load(['representations' => function ($query) {
            $query->withPivot('quantity', 'price_id');
        }]));
    }

    public function destroy($id)
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['message' => 'Réservation introuvable.'], 404);
        }

        $this->authorize('delete', $reservation);

        $reservation->delete();
        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }
}
