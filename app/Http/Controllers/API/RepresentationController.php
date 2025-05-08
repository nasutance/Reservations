<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Representation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RepresentationController extends Controller
{
    use AuthorizesRequests;
    
    public function index(Request $request)
    {
        $query = Representation::query();

        if ($request->has('show')) {
            $showTitle = $request->query('show');
            $query->whereHas('show', fn($q) => $q->where('title', 'like', "%$showTitle%"));
        }

        if ($request->has('location')) {
            $locationName = $request->query('location');
            $query->whereHas('location.locality', fn($q) => $q->where('locality', 'like', "%$locationName%"));
        }

        return response()->json($query->with(['show', 'location'])->get(), 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Representation::class);

        $validated = $request->validate([
            'schedule' => 'required|date',
            'show_id' => 'required|integer|exists:shows,id',
            'location_id' => 'required|integer|exists:locations,id'
        ]);

        $representation = Representation::create($validated);
        return response()->json($representation, 201);
    }

    public function show(Request $request, $id)
    {
        $representation = Representation::with(['show', 'location', 'reservations'])->find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée.'], 404);
        }

        $representation->reservations_links = $representation->reservations->map(fn($reservation) => [
            'id' => $reservation->id,
            'link' => route('reservations.show', ['reservation' => $reservation->id]),
        ]);

        unset($representation->reservations);

        return response()->json($representation);
    }

    public function update(Request $request, $id)
    {
        $representation = Representation::find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $this->authorize('update', $representation);

        $validated = $request->validate([
            'schedule' => 'sometimes|date',
            'show_id' => 'sometimes|integer|exists:shows,id',
            'location_id' => 'sometimes|integer|exists:locations,id'
        ]);

        $representation->update($validated);
        return response()->json($representation, 200);
    }

    public function destroy($id)
    {
        $representation = Representation::find($id);

        if (!$representation) {
            return response()->json(['message' => 'Représentation non trouvée'], 404);
        }

        $this->authorize('delete', $representation);

        $representation->delete();
        return response()->json(['message' => 'Représentation supprimée'], 200);
    }
}
