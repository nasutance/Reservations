<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function attach(Request $request, Show $show)
    {
      $this->authorize('addTag', $show);

        $request->validate([
            'tag_id' => 'required|exists:tags,id',
        ]);

        if (!$show->tags->contains($request->tag_id)) {
            $show->tags()->attach($request->tag_id);
        }

        return redirect()->back()->with('success', 'Mot-clé ajouté avec succès.');
    }
}
