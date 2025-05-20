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
    // Mixin Laravel pour l'autorisation avec les policies
    use AuthorizesRequests;

    /**
     * Exporte la liste des spectacles au format CSV
     * Accessible uniquement si la policy 'export' est autorisée
     */
    public function export(): StreamedResponse
    {
        $this->authorize('export', Show::class); // Sécurité : policy Laravel

        $filename = 'spectacles_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Encodage BOM UTF-8 pour Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // En-têtes de colonnes
            fputcsv($handle, ['ID', 'Titre', 'Description', 'Durée', 'Réservable']);

            // Parcours des spectacles
            Show::all()->each(function ($show) use ($handle) {
                $clean = fn($str) => str_replace(["\r\n", "\n", "\r"], ' ', $str); // Nettoyage des retours à la ligne

                // Ligne de données
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

    /**
     * Importe des spectacles depuis un fichier CSV
     */
    public function import(Request $request)
    {
        $this->authorize('create', Show::class); // Vérifie les droits

        // Validation du fichier uploadé
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        $headers = fgetcsv($handle);
        $headers[0] = preg_replace('/\x{FEFF}/u', '', $headers[0]); // Suppression du BOM UTF-8

        // Parcours ligne par ligne
        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row); // Associe les valeurs aux noms de colonnes

            // Validation des données
            Validator::make($data, [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'nullable|numeric|min:1',
                'bookable' => 'required|in:0,1',
            ])->validate();

            // Insère ou met à jour un spectacle
            Show::updateOrCreate(
                ['title' => $data['title']],
                [
                    'description' => $data['description'],
                    'duration' => $data['duration'],
                    'bookable' => $data['bookable'],
                    'created_in' => now()->year, // Champ supplémentaire pour le tracking
                    'slug' => Str::slug($data['title']),
                ]
            );
        }

        fclose($handle);

        return Inertia::location('/dashboard');
    }

    /**
     * Affiche la liste des spectacles avec filtres dynamiques (search, tri, tag, etc.)
     */
    public function index(Request $request)
    {
        $query = Show::query();

        // Recherche texte libre sur titre ou description
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        // Filtre par artiste (prénom + nom concaténés)
        if ($request->filled('artist')) {
            $query->whereHas('artistTypes.artist', fn ($q) =>
                $q->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$request->artist}%"])
            );
        }

        // Filtre par localité
        if ($request->filled('location')) {
            $query->whereHas('location.locality', fn ($q) =>
                $q->where('locality', 'like', "%{$request->location}%")
            );
        }

        // Filtre par code postal d'une représentation
        if ($request->filled('postal_code')) {
            $query->whereHas('representations.location', fn ($q) =>
                $q->where('locality_postal_code', $request->postal_code)
            );
        }

        // Filtres sur la durée
        if ($request->filled('min_duration')) {
            $query->where('duration', '>=', $request->min_duration);
        }

        if ($request->filled('max_duration')) {
            $query->where('duration', '<=', $request->max_duration);
        }

        // Tri dynamique
        if ($request->filled('sort')) {
            $query->orderBy($request->sort, $request->get('direction', 'asc'));
        }

        // Filtres par tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) =>
                $q->where('id', $request->tag)
            );
        }

        // Exclure les spectacles avec certains tags
        if ($request->filled('without_tag')) {
            $query->whereDoesntHave('tags', fn ($q) =>
                $q->where('tags.id', $request->without_tag)
            );
        }

        $query->withCount('representations'); // Pour afficher le nombre de représentations

        return Inertia::render('Show/Index', [
            'shows' => $query->paginate($request->get('per_page', 10))->withQueryString(),
            'filters' => $request->only([
                'q', 'artist', 'location', 'postal_code',
                'min_duration', 'max_duration', 'sort', 'direction', 'tag'
            ]),
            'tags' => Tag::all(['id', 'tag']), // Pour les filtres côté Vue
        ]);
    }

    /**
     * Affiche les détails d’un spectacle
     */
    public function show(string $id)
    {
        $show = Show::with([
            'artistTypes.artist',
            'artistTypes.type',
            'representations.location',
            'location',
            'tags',
            'videos'
        ])->findOrFail($id); // Retourne 404 si non trouvé

        return Inertia::render('Show/Show', [
            'show' => $show,
            'allTags' => Tag::all(['id', 'tag']),
        ]);
    }

    /**
     * Met à jour un spectacle existant
     */
    public function update(Request $request, Show $show)
    {
        $this->authorize('update', $show);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|numeric|min:1',
            'bookable' => 'required|boolean',
            'price_ids' => 'nullable|array',
            'price_ids.*' => 'exists:prices,id', // Sécurité des ID
        ]);

        // Mise à jour du spectacle
        $show->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'bookable' => (bool) $validated['bookable'],
        ]);

        // Synchronise les prix associés
        $show->prices()->sync($validated['price_ids'] ?? []);

        return Inertia::location('/dashboard');
    }

    /**
     * Supprime un spectacle
     */
    public function destroy(Show $show)
    {
        $this->authorize('delete', $show);
        $show->delete();

        return Inertia::location('/dashboard');
    }
}

