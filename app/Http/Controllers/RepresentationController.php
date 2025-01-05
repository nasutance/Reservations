<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Representation;
use Carbon\Carbon;

class RepresentationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('representation.index', [
            'representations' => Representation::all(),
        ]);
    }

    // …

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $representation = Representation::find($id);
        $date = Carbon::parse($representation->schedule)->format('d/m/Y');
        $time = Carbon::parse($representation->schedule)->format('G:i');

        return view('representation.show', [
            'representation' => $representation,
            'rep_date' => $date,
            'rep_time' => $time,
        ]);
    }

    // …
}
