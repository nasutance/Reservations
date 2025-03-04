<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ArtistController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\ShowController;
use App\Http\Controllers\API\ReservationController;

Route::post('/login', function (Request $request) {
  $request->validate([
    'email' => 'required|email',
    'password' => 'required',
  ]);
  $user = User::where('email', $request->email)->first();
  if (!$user || !Hash::check($request->password, $user->password)) {
    return response()->json(['message' => 'Invalid credentials'], 401);
  }
  $token = $user->createToken('api-token')->plainTextToken;
  return response()->json(['token' => $token], 200);
});

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
  });
  Route::get('/user', function (Request $request) {
    return response()->json($request->user()->load('roles'));
  });
});
//Route::apiResource('artists', ArtistApiController::class)
//	->middleware('auth.basic');
Route::middleware('auth:sanctum')->group(function () {
  Route::apiResource('artists', ArtistController::class);
  Route::apiResource('shows', ShowController::class);
  Route::apiResource('reservations', ReservationController::class);
});
