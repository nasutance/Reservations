<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{

    public function index()
    {
        return Inertia::render('dashboard', [
            'users' => User::with('roles')->get()
        ]);
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
    
        return Inertia::location('/dashboard');
    }
    
    public function destroy(User $user)
    {
        $user->delete();
    
        return Inertia::location('/dashboard');
    }
    
}
