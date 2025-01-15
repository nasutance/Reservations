<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArtistController;

//Crée automatiquement les routes /api/artists/{id}
Route::apiResource('artists', ArtistController::class);
