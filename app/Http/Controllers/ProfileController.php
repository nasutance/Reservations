<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de profil de l'utilisateur connecté.
     * Cette méthode passe les données nécessaires au composant Inertia `Profile/Edit`.
     * On y retrouve notamment les informations du profil utilisateur et son statut d'email vérifié.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail, // Vérifie si l'utilisateur doit confirmer son email
            'status' => session('status'), // Statut flashé en session (par ex. succès après modification)
            'auth' => [
                'user' => [
                    'firstname' => $request->user()->firstname, // Prénom
                    'lastname' => $request->user()->lastname,   // Nom
                    'email' => $request->user()->email,         // Email
                    'login' => $request->user()->login,         // Identifiant de connexion
                    'langue' => $request->user()->langue,       // Langue choisie
                    'email_verified_at' => $request->user()->email_verified_at, // Date de vérification email
                ],
            ],
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur.
     * Utilise une classe de FormRequest `ProfileUpdateRequest` pour valider les données reçues.
     * Si l'email est modifié, on invalide sa vérification.
     * Puis les données sont sauvegardées, et on redirige vers la page d'édition du profil avec un message de succès.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Remplit l'utilisateur avec les données validées
        $request->user()->fill($request->validated());

        // Si l'email a été modifié, on invalide sa vérification
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save(); // Sauvegarde des modifications

        return Redirect::route('profile.edit')
            ->with('status', 'Profile updated successfully.');
    }

    /**
     * Supprime le compte utilisateur définitivement.
     * Vérifie d'abord que le mot de passe actuel est correct.
     * Déconnecte ensuite l'utilisateur, invalide sa session, supprime son compte,
     * puis redirige vers la page d’accueil.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Vérifie que l'utilisateur a fourni son mot de passe actuel
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout(); // Déconnexion de l'utilisateur

        $user->delete(); // Suppression du compte

        // Invalidation de la session actuelle pour des raisons de sécurité
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/'); // Redirection vers l'accueil
    }
}
