<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use App\Models\Notification;

class AlbumController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $sortBy = $request->get('sort_by', 'titre');

        $albums = Album::where('user_id', $userId)
            ->orWhereHas('users', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->orderBy($sortBy, $sortBy === 'titre' ? 'asc' : 'desc')
            ->get();

        foreach ($albums as $album) {
            $cover = Photo::where('album_id', $album->id)
                ->orderBy('id', 'asc')
                ->first();
            $album->cover = $cover ? $cover->url : "";
        }

        $albumIds = $albums->pluck('id');
        $photos = Photo::whereIn('album_id', $albumIds)
            ->orderByDesc('id')
            ->get(['id', 'url', 'titre']);

        $users = User::all();

        return view('album.grid', [
            'albums' => $albums,
            'photos' => $photos,
            'users' => $users,
        ]);
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        if ($album->user_id !== auth()->id()) {
            return redirect("/albums")->with('toast', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas supprimer cet album, vous n\'en êtes pas le créateur.'
            ]);
        }
        $album->delete();
        return redirect("/albums")->with('toast', [
            'type' => 'success',
            'message' => 'Album supprimé avec succès !'
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            "titre" => "required|string|max:30"
        ]);

        $validate['creation'] = now()->format('Y-m-d H:i:s');
        $validate['user_id'] = auth()->id();

        $album = new Album($validate);
        $album->save();

        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'success',
            'title' => 'Nouvel album créé',
            'message' => 'Vous avez créé l’album : ' . $album->titre,
        ]);

        return redirect("/albums/$album->id")->with('toast', [
            'type' => 'success',
            'message' => 'Album créé avec succès !'
        ]);
    }

    public function share(Request $request)
    {
        $validated = $request->validate([
            'album_id' => 'required|exists:albums,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $album = Album::findOrFail($request->album_id);
        $userId = $request->user_id;

        if ($album->users()->where('users.id', $userId)->exists()) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Cet utilisateur a déjà accès à cet album.'
            ]);
        }

        $album->users()->syncWithoutDetaching([$userId]);

        Notification::create([
            'user_id' => $userId,
            'type' => 'success',
            'title' => 'Nouvel album partagé',
            'message' => 'Un album vient de vous être partagé : ' . $album->titre,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Album partagé avec succès !'
        ]);
    }

    public function show($id, Request $request)
    {
        $album = Album::findOrFail($id);
        $userId = auth()->id();

        $hasAccess = $album->user_id == $userId || $album->users()->where('users.id', $userId)->exists();
        if (!$hasAccess) {
            return redirect('/albums')->with('toast', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas accéder à cet album.'
            ]);
        }

        $photosQuery = $album->photos()->orderByDesc('id');

        if ($request->filled('tag')) {
            $photosQuery->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        $photos = $photosQuery->get(['id', 'url', 'titre']);

        $tags = Tag::all();
        $users = User::all();

        return view('photos.grid', [
            'album' => $album,
            'photos' => $photos,
            'users' => $users,
            'tags' => $tags
        ]);
    }
}