<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PhotosController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, "index"]);

Route::middleware('auth')->group(function () {
    Route::resource('albums', AlbumController::class)->except(['edit', 'create']);
    Route::resource('photos', PhotosController::class)->except(['edit']);

    Route::post('/albums/share', [AlbumController::class, 'share'])->name('albums.share');
});

// Recherche
Route::get('/search', [SearchController::class, 'search']);

// Tags
Route::post('/tags/create', [TagsController::class, 'store'])->name('tags.create');