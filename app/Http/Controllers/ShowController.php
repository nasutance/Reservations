<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Show;

class ShowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('show.index', [
            'shows' => Show::all(),
        ]);
    }

    // …

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('show.show', [
            'show' => Show::find($id),
        ]);
    }

    // …
}
