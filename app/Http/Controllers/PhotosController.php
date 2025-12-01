<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;
use App\Models\Photo;

class PhotosController extends Controller
{
    public function getPhotos($id)
    {
        $album = Album::findOrFail($id);
        return view('photos.grid', ['photos' => $album->photos, 'albumId' => $id]);
    }

    public function addPhotos(Request $request)
    {
        $validate = $request->validate([
            "titre" => "required|string|max:255",
            "url" => "required|url|max:255",
            "note" => "required|integer|min:1|max:5",
            "album_id" => "required|exists:albums,id"
        ]);

        Photo::create($validate)->save();

        return redirect("/album/{$validate['album_id']}")->with('success', 'Photo ajoutée avec succès !');
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);
        $albumId = $photo->album_id;
        $photo->delete();

        return redirect("/album/$albumId")->with('success', 'Photo supprimée avec succès !');
    }

    public function show($id)
    {
        $photo = Photo::findOrFail($id);
        $allPhotos = Album::findOrFail(id: $photo -> album_id);
        return view('photos.show', [
            "photo" => $photo,
            "photos" => $allPhotos -> photos
        ]);
    }
}