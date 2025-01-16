<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArtistController;

Route::middleware('auth:sanctum')->group(function () {
  //Crée automatiquement les routes /api/artists/{id}
  Route::apiResource('artists', ArtistController::class);
});
