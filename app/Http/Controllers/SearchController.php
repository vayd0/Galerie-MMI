<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');
        $userId = Auth::id();

        $photos = Photo::where('titre', 'like', "%$q%")
            ->whereHas('album', function($q2) use ($userId) {
                $q2->where('user_id', $userId);
            })
            ->with('album.user')
            ->get();

        foreach ($photos as $photo) {
            $photo->username = $photo->album && $photo->album->user ? $photo->album->user->name : 'Inconnu';
        }

        $albums = Album::where('titre', 'like', "%$q%")
            ->where('user_id', $userId)
            ->with('user')
            ->get();

        foreach ($albums as $album) {
            $album->username = $album->user ? $album->user->name : 'Inconnu';
        }

        return response()->json([
            'photos' => $photos,
            'albums' => $albums,
        ]);
    }
}
