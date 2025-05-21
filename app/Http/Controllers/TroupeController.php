<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TroupeController extends Controller
{   
    public function store(Request $request)
    {
        $this->authorize('create', Troupe::class);
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo_url' => 'nullable|image',
        ]);
           
        Troupe::create($validated);
    
        return back()->with('success', 'Troupe créée');
    }
    
    public function destroy(Troupe $troupe)
    {
        $this->authorize('delete', $troupe);
        $troupe->delete();
    
        return back()->with('success', 'Troupe supprimée');
    }
    
}
