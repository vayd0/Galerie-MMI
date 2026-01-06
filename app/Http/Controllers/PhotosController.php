<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\PossedeTag;
use App\Models\User;
class PhotosController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            "titre" => "required|string|max:255",
            "note" => "required|integer|min:1|max:5",
            "album_id" => "required|exists:albums,id",
            "url" => "nullable|string|max:255",
            "photo_file" => "nullable|image|max:51200" // 50MB en KB
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('photos', 'public');
            $url = '/storage/' . $path;
        } elseif ($request->filled('url')) {
            $url = $request->input('url');
        } else {
            return back()->withErrors(['url' => 'Veuillez fournir une URL ou un fichier image.']);
        }

        $photo = new Photo([
            "titre" => $request->input('titre'),
            "url" => $url,
            "note" => $request->input('note'),
            "album_id" => $request->input('album_id')
        ]);
        $photo->save();

        $tags = $request->input('tags');
        if ($tags) {
            foreach (is_array($tags) ? $tags : explode(',', $tags) ?? [] as $tagName) {
                $tag = Tag::firstOrCreate(['nom' => $tagName]);
                PossedeTag::firstOrCreate([
                    'photo_id' => $photo->id,
                    'tag_id' => $tag->id
                ]);
            }
        }

        return redirect("/albums/{$request->input('album_id')}")->with('success', 'Photo ajoutée avec succès !');
    }

    public function destroy($id)
    {
        $photo = Photo::findOrFail($id);
        $albumId = $photo->album_id;
        $photo->delete();

        return redirect("/albums/$albumId")->with('success', 'Photo supprimée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $album = Album::findOrFail($photo->album_id);
        
        $userId = auth()->id();
        $hasAccess = $album->user_id == $userId || $album->users()->where('users.id', $userId)->exists();
        if (!$hasAccess) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Vous ne pouvez pas modifier cette photo.'
            ]);
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'note' => 'required|integer|min:1|max:5',
            'photo_file' => 'nullable|image|max:51200', // 50MB en KB
            'tags' => 'nullable|string'
        ]);

        $photo->titre = $validated['titre'];
        $photo->note = $validated['note'];

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('photos', 'public');
            $photo->url = '/storage/' . $path;
        }

        $photo->save();

        PossedeTag::where('photo_id', $photo->id)->delete();
        
        if (!empty($validated['tags'])) {
            $tagNames = array_filter(array_map('trim', explode(',', $validated['tags'])));
            
            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = Tag::firstOrCreate(['nom' => $tagName]);
                    PossedeTag::firstOrCreate([
                        'photo_id' => $photo->id,
                        'tag_id' => $tag->id
                    ]);
                }
            }
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Photo modifiée avec succès !'
        ]);
    }

    public function show($id)
    {
        $photo = Photo::with('tags')->findOrFail($id);
        $album = Album::findOrFail($photo->album_id);
        $userId = auth()->id();

        $hasAccess = $album->user_id == $userId || $album->users()->where('users.id', $userId)->exists();
        if (!$hasAccess) {
            abort(403, 'Vous ne pouvez pas accéder à cette photo.');
        }

        $tags = Tag::pluck('nom')->toArray();
        return view('photos.show', [
            "photo" => $photo,
            "photos" => $album->photos,
            "tags" => $tags
        ]);
    }
}