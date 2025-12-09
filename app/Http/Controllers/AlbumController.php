<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;
use App\Models\Photo;

class AlbumController extends Controller
{
    public function getAlbum()
    {
        $userId = auth()->id();
        $albums = Album::where('user_id', $userId)->get();


        foreach ($albums as $album) {
            $cover = Photo::where('album_id', $album->id)
                ->orderBy('id', 'asc')
                ->first();
            $album->cover = $cover ? $cover->url : "";
        }

        $albumIds = $albums->pluck('id');
        $photos = Photo::whereIn('album_id', $albumIds)
            ->orderByDesc('id')
            ->get(['id', 'url', 'titre']);

        return view('album.grid', [
            'albums' => $albums,
            'photos' => $photos
        ]);
    }

    public function deleteAlbum($id)
    {
        $album = Album::findOrFail($id);
        if ($album->user_id !== auth()->id()) {
            abort(403, 'Vous ne pouvez pas supprimer cet album.');
        }
        $album->delete();
        return redirect("/albums")->with('success', 'Album supprimé avec succès !');
    }

    public function addAlbum(Request $request)
    {
        $validate = $request->validate([
            "titre" => "required|string|max:30"
        ]);

        $validate['creation'] = now()->format('Y-m-d H:i:s');
        $validate['user_id'] = auth()->id();

        $album = new Album($validate);
        $album->save();

        return redirect("/albums/$album->id")->with('success', 'Album créé avec succès !');
    }
    public function share(Request $request)
    {
        $request->validate([
            'album_id' => 'required|exists:albums,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $album = Album::findOrFail($request->album_id);
        $userId = $request->user_id;

        // Exemple avec une table pivot album_user
        // (crée la relation dans le modèle Album : users())
        $album->users()->syncWithoutDetaching([$userId]);

        return back()->with('success', 'Album partagé avec succès !');
    }
}