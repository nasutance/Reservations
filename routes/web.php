<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{
    ProfileController,
    ArtistController,
    TypeController,
    PriceController,
    LocalityController,
    RoleController,
    LocationController,
    ShowController,
    RepresentationController,
    TagController,
    ReservationController,
    DashboardController,
    UserController
};

// Page d’accueil
Route::get('/', fn () => Inertia::render('Home'));

// Dashboard (authentifié + email vérifié)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Gestion du profil
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes RESTful pour les ressources principales
Route::resources([
    'artist' => ArtistController::class,
    'type' => TypeController::class,
    'price' => PriceController::class,
    'locality' => LocalityController::class,
    'role' => RoleController::class,
    'show' => ShowController::class,
    'location' => LocationController::class,
    'representation' => RepresentationController::class,

]);

// Ressource "user" sans les routes POST
Route::resource('users', UserController::class)->except(['create', 'store']);


// Routes spécifiques aux réservations
Route::middleware('auth')->group(function () {
    Route::get('/reservation/{show}', [ReservationController::class, 'create'])->name('reservation.create');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservation.index');
    Route::get('/reservation/details/{reservation}', [ReservationController::class, 'show'])->name('reservation.show');
    Route::patch('/reservation/{reservation}', [ReservationController::class, 'update'])->name('reservation.update');
    Route::delete('/reservation/{reservation}', [ReservationController::class, 'destroy'])->name('reservation.destroy');
    Route::post('/reservation/{reservation}/add-line', [ReservationController::class, 'addLine'])->name('reservation.addLine');
});

// Routes personnalisées pour les tags
Route::middleware(['auth'])->group(function () {
    Route::post('/shows/{show}/tags', [TagController::class, 'attach'])->name('show.attachTag');
});

// Page de remerciement après une réservation
Route::get('/merci', fn () => Inertia::render('Reservation/Thanks', [
    'reservationId' => session('reservationId'),
]))->middleware('auth')->name('reservation.thanks');

// Auth routes Laravel Breeze
require __DIR__.'/auth.php';

Route::middleware(['auth', 'can:create,App\Models\Show'])->group(function () {
    Route::post('/shows/import', [ShowController::class, 'import'])->name('shows.import');
    Route::get('/shows/export', [ShowController::class, 'export'])->name('shows.export');
});
