<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PhotosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

// Index
Route::get('/', [HomeController::class, "index"]);

// Routes albums
Route::get('/albums', [AlbumController::class, "getAlbum"])->middleware("auth");
Route::get('/albums/{id}', [PhotosController::class, "getPhotos"])->where('id', '[0-9]+')->middleware("auth");
Route::post("/albums/add", [AlbumController::class, "addAlbum"])->name("albums.add")->middleware("auth");
Route::delete("/albums/delete/{id}", [AlbumController::class, "deleteAlbum"])->name("albums.delete")->middleware("auth");

// Routes photos
Route::get('/photos/{id}', [PhotosController::class, 'show'])->name('photos.show')->where('id', '[0-9]+')->middleware("auth");
Route::post("/photos/add", [PhotosController::class, "addPhotos"])->name("photos.add")->middleware("auth");
Route::post("/photos/delete", [PhotosController::class, "deletePhotos"])->name("photos.delete")->middleware("auth");
Route::delete('/photos/{id}', [PhotosController::class, 'destroy'])->name('photos.destroy')->where('id', '[0-9]+')->middleware("auth");