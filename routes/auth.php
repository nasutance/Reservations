<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| Routes pour les invités (non authentifiés)
|--------------------------------------------------------------------------
| Ces routes sont accessibles uniquement aux utilisateurs non connectés.
| Elles concernent l'inscription, la connexion, la réinitialisation du mot de passe.
*/
Route::middleware('guest')->group(function () {

    // Affiche le formulaire d'inscription
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Traite la soumission du formulaire d'inscription
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Affiche le formulaire de connexion
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Traite la soumission du formulaire de connexion
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Affiche le formulaire de demande de réinitialisation du mot de passe
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Envoie l’email avec le lien de réinitialisation
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Affiche le formulaire de réinitialisation du mot de passe via le lien reçu par email
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Traite la soumission du nouveau mot de passe
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Routes pour les utilisateurs authentifiés
|--------------------------------------------------------------------------
| Ces routes sont accessibles uniquement si l'utilisateur est connecté.
| Elles couvrent la vérification d'email, la confirmation de mot de passe,
| la mise à jour du mot de passe et la déconnexion.
*/
Route::middleware('auth')->group(function () {

    // Invite l’utilisateur à vérifier son adresse email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Valide l’email via le lien reçu
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1']) // Signature de l’URL + limitation des tentatives
        ->name('verification.verify');

    // Envoie une nouvelle notification de vérification d’email
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Affiche le formulaire de confirmation du mot de passe
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Traite la confirmation du mot de passe
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Met à jour le mot de passe de l'utilisateur
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // Déconnecte l'utilisateur
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
