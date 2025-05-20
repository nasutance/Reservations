<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Contrôleurs API
use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\ShowController;
use App\Http\Controllers\API\ReservationController;
use App\Http\Controllers\API\RepresentationController;
use App\Http\Controllers\ImportExternalDataController;

/*
|--------------------------------------------------------------------------
| Routes API publiques
|--------------------------------------------------------------------------
| Ces routes sont accessibles sans authentification.
*/

// Route de connexion utilisateur - Retourne un token en cas de succès
Route::post('/login', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    // Vérifie les identifiants
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    // Crée un token d'accès API
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token], 200);
});

/*
|--------------------------------------------------------------------------
| Routes API protégées (auth:sanctum)
|--------------------------------------------------------------------------
| Ces routes nécessitent une authentification par token via Sanctum.
*/

Route::middleware('auth:sanctum')->group(function () {

    // Création d’un nouveau token à partir d’un nom fourni
    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);
        return ['token' => $token->plainTextToken];
    });

    // Récupération des informations de l’utilisateur connecté avec ses rôles
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('roles'));
    });

    /*
    |--------------------------------------------------------------------------
    | API Resources protégées
    |--------------------------------------------------------------------------
    | Ces routes utilisent les contrôleurs RESTful pour gérer les entités.
    */

    Route::apiResource('artists', ArtistController::class);
    Route::apiResource('shows', ShowController::class);
    Route::apiResource('reservations', ReservationController::class);
    Route::apiResource('representations', RepresentationController::class);
});

/*
|--------------------------------------------------------------------------
| Routes d’importation de données externes
|--------------------------------------------------------------------------
| Ces routes permettent d’importer des données XML ou CSV via un contrôleur.
*/

//Route::post('/import-localities',        [ImportExternalDataController::class, 'importLocalities']);
//Route::post('/import-locations',         [ImportExternalDataController::class, 'importLocations']);
//Route::post('/import-shows',             [ImportExternalDataController::class, 'importShows']);
//Route::post('/import-representations',   [ImportExternalDataController::class, 'importRepresentations']);
