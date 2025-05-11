<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs avec leurs rôles dans le tableau de bord admin.
     * Utilise Inertia pour rendre une vue Vue.js (ex: resources/js/Pages/dashboard.vue).
     */
    public function index()
    {
        return Inertia::render('dashboard', [
            'users' => User::with('roles')->get() // Chargement eager des rôles liés
        ]);
    }

    /**
     * Met à jour les informations d’un utilisateur ainsi que ses rôles.
     * Vérifie et synchronise les rôles dans la table pivot user_role.
     */
    public function update(Request $request, User $user)
    {
        // Validation des données du formulaire
        $validated = $request->validate([
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'email' => 'required|email|max:255',
            'langue' => 'nullable|string|max:5',
            'roles' => 'array', // Attendu : tableau d’identifiants de rôles
            'roles.*' => 'exists:roles,id', // Vérifie l’existence de chaque rôle
        ]);

        // Mise à jour de l’utilisateur avec les données validées
        $user->update($validated);

        // Synchronisation des rôles (remplace les rôles existants)
        $user->roles()->sync($validated['roles'] ?? []);

        // Redirection vers le tableau de bord (Inertia)
        return Inertia::location('/dashboard');
    }

    /**
     * Supprime un utilisateur de la base de données.
     * ⚠ À sécuriser si des dépendances (réservations, commentaires) existent.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return Inertia::location('/dashboard');
    }
}
