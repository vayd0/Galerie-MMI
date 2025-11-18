<?php

use App\Http\Controllers\PhotosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

Route::get('/', [AlbumController::class, "getAlbum"]);
Route::get('/album/{id}', [PhotosController::class, "getPhotos"])->where('id', '[0-9]+');
Route::post("/albums/add", [AlbumController::class, "addAlbum"])->name("albums.add");
Route::get('/photos/{id}', [PhotosController::class, 'show'])->name('photos.show');
Route::post("/photos/add", [PhotosController::class, "addPhotos"])->name("photos.add");