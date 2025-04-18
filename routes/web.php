<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\LocalityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\RepresentationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ReservationController;

Route::middleware(['auth', 'admin'])->group(function () {
  Route::post('/shows/{show}/tags', [TagController::class, 'attach'])->name('show.attachTag');
});
Route::get('/shows/without-tag/{tag}', [ShowController::class, 'withoutTag'])->name('show.withoutTag');


Route::get('/', function () {
  return Inertia::render('Home');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
          ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
          ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
          ->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/artist', [ArtistController::class, 'index'])
        ->name('artist.index');
Route::get('/artist/{id}', [ArtistController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('artist.show');

Route::middleware('auth')->group(function () {
  Route::get('/artist/create', [ArtistController::class, 'create'])
        ->name('artist.create');
  Route::post('/artist', [ArtistController::class, 'store'])
        ->name('artist.store');
  Route::get('/artist/edit/{id}', [ArtistController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('artist.edit');
  Route::put('/artist/{id}', [ArtistController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('artist.update');
  Route::delete('/artist/{id}', [ArtistController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('artist.delete');
});
Route::get('/type', [TypeController::class, 'index'])->name('type.index');
Route::get('/type/{id}', [TypeController::class, 'show'])
        ->where('id', '[0-9]+')->name('type.show');

Route::get('/price', [PriceController::class, 'index'])->name('price.index');
Route::get('/price/{id}', [PriceController::class, 'show'])
        ->where('id', '[0-9]+')->name('price.show');
Route::get('/locality', [LocalityController::class, 'index']) ->name('locality.index');
Route::get('/locality/{postal_code}', [LocalityController::class, 'show'])
        ->where('postal_code', '[0-9]+')->name('locality.show');
Route::get('/role', [RoleController::class, 'index'])->name('role.index');
Route::get('/role/{id}', [RoleController::class, 'show'])
        ->where('id', '[0-9]+')->name('role.show');

Route::get('/show', [ShowController::class, 'index'])->name('show.index');
Route::get('/show/{id}', [ShowController::class, 'show'])
       ->where('id', '[0-9]+')->name('show.show');

Route::get('/location', [LocationController::class, 'index'])->name('location.index');
Route::get('/location/{id}', [LocationController::class, 'show'])
        ->where('id', '[0-9]+')->name('location.show');

Route::get('/representation', [RepresentationController::class, 'index'])
        ->name('representation.index');
Route::get('/representation/{id}', [RepresentationController::class, 'show'])
        ->where('id', '[0-9]+')->name('representation.show');

Route::middleware(['auth'])->group(function () {
  Route::get('/reservation/{show}', [ReservationController::class, 'create'])->name('reservation.create');
  Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
  Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservation.index');
  Route::get('/reservation/details/{reservation}', [ReservationController::class, 'show'])->name('reservation.show');
  Route::delete('/reservation/{reservation}', [ReservationController::class, 'destroy'])->name('reservation.destroy');
});
