@props(['album' => null, 'users' => null, 'selectedUserId' => null])
<div id="shareAlbumModal" class="fixed inset-0 z-50 hidden bg-black/20 backdrop-blur-sm items-center justify-center">
    <div class="relative bg-white p-6 rounded-lg shadow-lg w-[350px]" data-modal-content>
        <h2 class="text-lg font-bold mb-4">Partager l'album</h2>
        <form method="POST" action="{{ route('albums.share') }}">
            @csrf
            <input type="hidden" name="album_id" value="{{ $album->id }}">
            <label for="user_id" class="block mb-2">Sélectionnez un utilisateur :</label>
            <select name="user_id" id="user_id" class="w-full mb-4 p-2 border rounded">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
            <div class="flex justify-end space-x-2">
                <button type="button" class="px-3 py-1 bg-gray-300 rounded close-modal"
                    data-close-modal>Annuler</button>
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded">Partager</button>
            </div>
        </form>
    </div>
</div>