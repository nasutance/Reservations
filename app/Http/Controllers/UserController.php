<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Met à jour les informations d’un utilisateur ainsi que ses rôles.
     * Vérifie et synchronise les rôles dans la table pivot user_role.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

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

        // Seul un admin peut modifier les rôles : un utilisateur ne peut pas s'auto-promouvoir
        if ($request->user()->hasRole('admin')) {
            $user->roles()->sync($validated['roles'] ?? []);
        }

        // Redirection vers le tableau de bord (Inertia)
        return Inertia::location('/dashboard');
    }

    /**
     * Supprime (logiquement) un utilisateur.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return Inertia::location('/dashboard');
    }
}
