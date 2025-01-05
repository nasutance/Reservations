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
     $show = Show::find($id);

     // Récupérer les artistes du spectacle et les grouper par type
     $collaborateurs = [
         'auteur' => [],
         'scénographe' => [],
         'comédien' => [],
     ];

     foreach ($show->artistTypes as $at) {
         $collaborateurs[$at->type->type][] = $at->artist;
     }

     return view('show.show', [
         'show' => $show,
         'collaborateurs' => $collaborateurs,
     ]);
 }

}
