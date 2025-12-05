<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;

class AlbumController extends Controller
{
    public function getAlbum()
    {
        $userId = auth()->id();
        $albums = Album::where('user_id', $userId)->get();

        foreach ($albums as $album) {
            $cover = DB::select(
                "SELECT url FROM Photos WHERE album_id = ? ORDER BY id ASC LIMIT 1",
                [$album->id]
            );
            $album->cover = isset($cover[0]) ? $cover[0]->url : "";
        }
        return view('album.grid', ['albums' => $albums]);
    }

        public function deleteAlbum($id)
    {
            Album::findOrFail($id)->delete();
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
}