<?php

namespace App\Models;

// Importation des classes nécessaires
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

// Le modèle Representation représente une date/heure précise pour un spectacle donné
class Representation extends Model implements Feedable // Intègre l'interface pour générer un flux RSS
{
    use HasFactory;

    /**
     * Attributs remplissables automatiquement (mass assignment)
     */
    protected $fillable = [
        'show_id',     // ID du spectacle concerné
        'schedule',    // Date et heure de la représentation
        'location_id', // ID du lieu où se déroule la représentation
    ];

    // Cast automatique du champ "schedule" en objet Carbon (date-heure)
    protected $casts = [
        'schedule' => 'datetime',
    ];
    
    // Nom de la table associée
    protected $table = 'representations';

    // Pas de timestamps automatiques (created_at, updated_at)
    public $timestamps = false;

    /**
     * Relation N-1 : une représentation se déroule dans un lieu
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Relation N-1 : une représentation est associée à un seul spectacle
     */
    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    /**
     * Relation N-N via table pivot 'representation_reservation'
     * Permet d’associer une représentation à des réservations avec quantité et tarif
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'representation_reservation')
                    ->withPivot('price_id', 'quantity');
    }

    /**
     * Relation 1-N potentiellement discutable : on peut associer plusieurs prix à une représentation
     * Attention : peut créer une confusion avec la relation Shows ↔ Prices
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Méthode requise par l’interface Feedable pour générer une liste d’éléments du flux RSS
     * On retourne ici les 20 prochaines représentations à venir, avec leur show et location préchargés
     */
    public static function getFeedItems(): Collection
    {
        return self::where('schedule', '>', now())
                   ->orderBy('schedule')
                   ->with('show', 'location')
                   ->take(20)
                   ->get(); // Retourne une Collection Laravel
    }

    /**
     * Convertit une instance de Representation en élément FeedItem pour le flux RSS
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create([
            'id' => route('representations.show', $this), // Lien vers la page de détail
            'title' => "{$this->show->title} à {$this->location->designation}", // Exemple : "Ayiti à Théâtre National"
            'summary' => "Le {$this->schedule->format('d/m/Y à H\hi')} à {$this->location->designation}",
            'updated' => new Carbon($this->schedule), // Date de la représentation comme mise à jour
            'link' => route('representations.show', $this), // Même lien que l’ID
            'authorName' => '', // Vide car pas d’auteur défini ici
        ]);
    }
}
