<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class PhotosController extends Controller
{
    public function getPhotos($id) 
    {
        $photos = DB::select("SELECT * FROM Photos WHERE album_id = ?", [$id]);
        return view('photos', ['photos' => $photos]);
    }
}
