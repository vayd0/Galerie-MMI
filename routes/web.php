<?php

use App\Http\Controllers\PhotosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

// Index
Route::get('/', [AlbumController::class, "getAlbum"]);

// Routes albums
Route::get('/album/{id}', [PhotosController::class, "getPhotos"])->where('id', '[0-9]+');
Route::post("/albums/add", [AlbumController::class, "addAlbum"])->name("albums.add");
Route::delete("/albums/{id}", [AlbumController::class, "deleteAlbum"])->name("albums.delete");

// Routes photos
Route::get('/photos/{id}', [PhotosController::class, 'show'])->name('photos.show')->where('id', '[0-9]+');
Route::post("/photos/add", [PhotosController::class, "addPhotos"])->name("photos.add");
Route::post("/photos/delete", [PhotosController::class, "deletePhotos"])->name("photos.delete");
Route::delete('/photos/{id}', [PhotosController::class, 'destroy'])->name('photos.destroy')->where('id', '[0-9]+');
