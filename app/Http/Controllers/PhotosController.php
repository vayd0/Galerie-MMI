<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class PhotosController extends Controller
{
    public function getPhotos() 
    {
        $photos = DB::select("SELECT * FROM Photos");
        return view('photo', ['albums' => $photos]);
    }
}
