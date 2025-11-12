<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AlbumController extends Controller
{
    public function getAlbum() 
    {
        $albums = DB::select("SELECT * FROM Albums");
        foreach ($albums as $album) {
            $cover = DB::select(
            "SELECT id, url FROM Photos WHERE album_id = ? ORDER BY id ASC LIMIT 1",
            [$album->id]
            );
            $album->cover = $cover[0];
        }
        return view('album', ['albums' => $albums]);
    }
}
