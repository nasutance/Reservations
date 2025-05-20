<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Importation des contrôleurs utilisés
use App\Http\Controllers\{
    FeedController,
    ProfileController,
    ArtistController,
    TypeController,
    PriceController,
    LocalityController,
    RoleController,
    LocationController,
    ShowController,
    RepresentationController,
    ReservationController,
    DashboardController,
    TagController,
    UserController,
    VideoController
};

// ====================
// Flux RSS (package Spatie)
// ====================
Route::feeds();

// ====================
// Page d’accueil (publique)
// ====================
Route::get('/', fn () => Inertia::render('Home'));

// ====================
// Tableau de bord (accessible uniquement aux utilisateurs authentifiés et vérifiés)
// ====================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ====================
// Gestion du profil utilisateur (authentifié)
// ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ====================
// Ressources RESTful classiques
// ====================
Route::resources([
    'artist'         => ArtistController::class,
    'type'           => TypeController::class,
    'price'          => PriceController::class,
    'locality'       => LocalityController::class,
    'role'           => RoleController::class,
    'show'           => ShowController::class,
    'location'       => LocationController::class,
    'representation' => RepresentationController::class,
]);

// ====================
// Ressource "user" (authentifié) — sans création ni insertion
// ====================
Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class)->except(['create', 'store']);
});

// ====================
// Gestion des réservations (authentifié)
// ====================
Route::middleware('auth')->group(function () {
    Route::get('/reservation/{show}', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/reservation/details/{reservation}', [ReservationController::class, 'show'])->name('reservation.show');
    Route::patch('/reservation/{reservation}', [ReservationController::class, 'update'])->name('reservation.update');
    Route::delete('/reservation/{reservation}', [ReservationController::class, 'destroy'])->name('reservation.destroy');

    // Ajouter une ligne (combinaison représentation + tarif) à une réservation existante
    Route::post('/reservation/{reservation}/add-line', [ReservationController::class, 'addLine'])->name('reservation.addLine');

    // Supprimer une ligne d’une réservation
    Route::delete('/reservation/{reservation}/line/{representation}/{price}', [ReservationController::class, 'destroyLine']);
});

// ====================
// Gestion des tags pour les spectacles (authentifié)
// ====================
Route::middleware(['auth'])->group(function () {
    Route::post('/shows/{show}/tags', [TagController::class, 'attach'])->name('show.attachTag');
});


// ====================
// Page de remerciement après une réservation (authentifié)
// ====================
Route::get('/merci', fn () => Inertia::render('Reservation/Thanks', [
    'reservationId' => session('reservationId'),
]))->middleware('auth')->name('reservation.thanks');

// ====================
// Authentification (routes générées par Laravel Breeze)
// ====================
require __DIR__.'/auth.php';

// ====================
// Import/Export des spectacles (réservé aux utilisateurs autorisés à créer un Show)
// ====================
Route::middleware(['auth', 'can:create,App\Models\Show'])->group(function () {
    Route::post('/shows/import', [ShowController::class, 'import'])->name('shows.import');
    Route::get('/shows/export', [ShowController::class, 'export'])->name('shows.export');
});

Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
Route::get('/videos/by-artist/{name}', [VideoController::class, 'byArtist']);
