<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlbumController;

Route::get('/', [AlbumController::class, "getAlbum"]);
Route::get('/album/', [AlbumController::class, "getAlbum"]);
