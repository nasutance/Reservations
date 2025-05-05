<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
     {
         $prices = Price::all();
         return view('price.index', [
             'prices' => $prices,
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
        //
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
         'price' => 'required|numeric|min:0',
     ]);

     $price->update($validated);

     return Inertia::location('/dashboard');
 }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
