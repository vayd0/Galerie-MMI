<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:50'
        ]);
        $nom = trim($request->input('nom'));
        $tag = Tag::firstOrCreate(['nom' => $nom]);
        return response()->json([
            'success' => true,
            'tag' => $tag->nom
        ]);
    }
}
