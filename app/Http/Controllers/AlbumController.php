<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;

class AlbumController extends Controller
{
    public function getAlbum()
    {
        $albums = Album::all();
        $noCover = "https://discourse.gohugo.io/t/image-is-not-shown-or-broken-on-webpage/22584";

        foreach ($albums as $album) {
            $cover = DB::select(
                "SELECT url FROM Photos WHERE album_id = ? ORDER BY id ASC LIMIT 1",
                [$album->id]
            );
            $album->cover = isset($cover[0]) ? $cover[0]->url : $noCover;
        }
        return view('album.grid', ['albums' => $albums]);
    }

        public function deleteAlbum($id)
    {
        Album::findOrFail($id) -> delete();

        return redirect("/album/$id");
    }

    public function addAlbum(Request $request)
    {
        $validate = $request->validate([
            "titre" => "required|string|max:30"
        ]);

        $validate['creation'] = now()->format('Y-m-d H:i:s');

        $album = Album::create($validate)->save();

        return redirect("/album/$album->id")->with('success', 'Album créé avec succès !');
    }
}