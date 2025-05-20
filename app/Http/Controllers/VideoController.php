<?php

namespace App\Http\Controllers;
use App\Models\Video;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;

class VideoController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $this->authorize('create', Video::class);
    
        $request->validate([
            'title' => 'required|max:255',
            'video_url' => 'required|url|max:255|unique:videos,video_url',
            'show_id' => 'required|exists:shows,id',
        ]);
    
        Video::create($request->all());
    
        return back()->with('success', 'Vidéo ajoutée.');
    }
    

}
