<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;
class PhotosController extends Controller
{
    public function getPhotos($id) 
    {
        $album = Album::findOrFail($id);
        return view('photos', ['photos' => $album -> photos]);
    }
}