<?php

use App\Http\Controllers\PhotosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

Route::get('/', [AlbumController::class, "getAlbum"]);
Route::get('/album/{id}', [PhotosController::class, "getPhotos"])->where('id', '[0-9]+');
Route::get("/albums/add", [AlbumController::class, "seeAddTemp"]);
Route::post("/albums/add", [AlbumController::class, "addAlbum"]);
