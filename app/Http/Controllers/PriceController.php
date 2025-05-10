<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;
use Inertia\Inertia;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('AdminDashboard', [
            'prices' => Price::all(),
            // autres props si nécessaire
        ]);
    }
    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    
        Price::create($validated);
    
        return Inertia::location('/dashboard');

    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      $price = Price::find($id);
      return view('price.show',[
        'price' => $price,
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Price $price)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    
        $price->update($validated);
    
        return Inertia::location('/dashboard');

    }
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Price $price)
    {
        $price->delete();
    
        return Inertia::location('/dashboard');

    }
}
