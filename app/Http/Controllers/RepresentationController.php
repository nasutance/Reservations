<?php

namespace App\Http\Controllers;

use App\Models\Representation;
use App\Models\Show;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RepresentationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Representation::class);

        return Inertia::render('Dashboard/Dashboard', [
            'representations' => Representation::with(['show', 'location'])->get(),
            'shows' => Show::all(),
            'locations' => Location::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Representation::class);

        $validated = $request->validate([
            'schedule' => 'required|date',
            'show_id' => 'required|exists:shows,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $representation = Representation::create($validated);

        return Inertia::location('/dashboard');
    }

    public function update(Request $request, $id)
    {
        $representation = Representation::findOrFail($id);

        $this->authorize('update', $representation);

        $validated = $request->validate([
            'schedule' => 'required|date',
            'show_id' => 'required|exists:shows,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $representation->update($validated);

        return Inertia::location('/dashboard');
    }

    public function destroy($id)
    {
        $representation = Representation::findOrFail($id);

        $this->authorize('delete', $representation);

        $representation->delete();

        return Inertia::location('/dashboard');
    }
}
