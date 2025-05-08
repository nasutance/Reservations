<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        return response()->json(
        \App\Models\User::with('roles')->get()
        );
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:60',
            'lastname' => 'required|string|max:60',
            'email' => 'required|email|max:255',
            'langue' => 'nullable|string|max:5',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update($validated);
        $user->roles()->sync($validated['roles'] ?? []);

        return response()->json([
            'message' => 'Utilisateur mis à jour',
            'user' => $user->load('roles')
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}
