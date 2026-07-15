<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriceController;
// Importation des contrôleurs utilisés
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepresentationController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\UserController;
use App\Models\Representation;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ====================
// Flux RSS (package Spatie)
// ====================
Route::feeds();

// ====================
// Page d’accueil (publique)
// ====================
Route::get('/', function () {
    $upcoming = Representation::with(['show', 'location'])
        ->where('schedule', '>=', now())
        ->orderBy('schedule')
        ->take(6)
        ->get();

    return Inertia::render('Home', ['upcoming' => $upcoming]);
});

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
// Catalogue des spectacles (public)
// ====================
Route::resource('show', ShowController::class)->only(['index', 'show']);

// ====================
// Gestion du contenu (authentifié — les policies restreignent aux admins)
// ====================
Route::middleware('auth')->group(function () {
    Route::resource('show', ShowController::class)->only(['update', 'destroy']);
    Route::resource('artist', ArtistController::class)->only(['store', 'update', 'destroy']);
    Route::resource('price', PriceController::class)->only(['store', 'update', 'destroy']);
    Route::resource('representation', RepresentationController::class)->only(['store', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['update', 'destroy']);
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
    Route::delete('/reservation/{reservation}/line/{representation}/{price}', [ReservationController::class, 'destroyLine'])->name('reservation.destroyLine');
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
