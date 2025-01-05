<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('location.index', [
            'locations' => Location::all(),
        ]);
    }

    // …

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('location.show', [
            'location' => Location::find($id),
        ]);
    }

    // …
}
