<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShowController extends Controller
{
    use AuthorizesRequests;

    public function export(): StreamedResponse
    {
        $this->authorize('export', Show::class);

        $filename = 'spectacles_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
        
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Titre', 'Description', 'Durée', 'Réservable']);

            Show::all()->each(function ($show) use ($handle) {
                $clean = fn($str) => str_replace(["\r\n", "\n", "\r"], ' ', $str);
            
                fputcsv($handle, [
                    $show->id,
                    $clean($show->title),
                    $clean($show->description),
                    $show->duration,
                    $show->bookable ? 'oui' : 'non',
                ]);
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
{
    $this->authorize('create', Show::class); 

    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');
    $handle = fopen($file->getRealPath(), 'r');

    $headers = fgetcsv($handle);

    // Nettoyage UTF-8 BOM
    $headers[0] = preg_replace('/\x{FEFF}/u', '', $headers[0]);

    while (($row = fgetcsv($handle)) !== false) {
        $data = array_combine($headers, $row);

        Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:1',
            'bookable' => 'required|in:0,1',
        ])->validate();

        Show::updateOrCreate(
            ['title' => $data['title']],
            [
                'description' => $data['description'],
                'duration' => $data['duration'],
                'bookable' => $data['bookable'],
                'created_in' => now()->year, // ← ici le fix
                'slug' => Str::slug($data['title']),
            ]
        );
        
    }

    fclose($handle);

    return Inertia::location('/dashboard');

}

    public function index(Request $request)
    {
        $query = Show::query();

        // 🔍 Filtres dynamiques
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('artist')) {
            $query->whereHas('artistTypes.artist', fn ($q) =>
                $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$request->artist}%"])
            );
        }

        if ($request->filled('location')) {
            $query->whereHas('location.locality', fn ($q) =>
                $q->where('locality', 'like', "%{$request->location}%")
            );
        }

        if ($request->filled('postal_code')) {
            $query->whereHas('representations.location', fn ($q) =>
                $q->where('locality_postal_code', $request->postal_code)
            );
        }

        if ($request->filled('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->filled('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) =>
                $q->where('id', $request->tag)
            );
        }

        if ($request->filled('without_tag')) {
            $query->whereDoesntHave('tags', fn ($q) =>
                $q->where('tags.id', $request->without_tag)
            );
        }

        // Relations
        $query->withCount('representations');

        return Inertia::render('Show/Index', [
            'shows' => $query->paginate($request->get('per_page', 10))->withQueryString(),
            'filters' => $request->only([
                'q', 'artist', 'location', 'postal_code',
                'min_duration', 'max_duration', 'sort', 'direction', 'tag'
            ]),
            'tags' => Tag::all(['id', 'tag']),
        ]);
    }

    public function show(string $id)
    {
        $show = Show::with([
            'artistTypes.artist',
            'artistTypes.type',
            'representations.location',
            'location',
            'tags'
        ])->findOrFail($id);

        return Inertia::render('Show/Show', [
            'show' => $show,
            'allTags' => Tag::all(['id', 'tag']),
        ]);
    }

    public function update(Request $request, Show $show)
    {
        $this->authorize('update', $show);
    
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:1',
            'bookable' => 'required|boolean',
            'price_ids' => 'nullable|array',
            'price_ids.*' => 'exists:prices,id',
        ]);
    
        $show->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'bookable' => (bool) $validated['bookable'],
        ]);
    
        $show->prices()->sync($validated['price_ids'] ?? []);
    
        return Inertia::location('/dashboard');
    }
    
    public function destroy(Show $show)
    {
        $this->authorize('delete', $show);
        $show->delete();
    
        return Inertia::location('/dashboard');
    }
    
}
