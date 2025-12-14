@props(['album' => null, 'users' => null, 'selectedUserId' => null])

<div id="shareAlbumModal" class="fixed inset-0 z-50 hidden bg-black/20 backdrop-blur-sm items-center justify-center">
    <div class="relative w-screen bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-96 max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
        data-modal-content>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Partager l'album</h2>
        <p class="text-sm text-gray-500 mb-6">Saisissez un pseudo pour partager cet album</p>
        <form method="POST" action="{{ route('albums.share') }}" class="space-y-6" autocomplete="off">
            @csrf
            <input type="hidden" name="album_id" value="{{ $album->id }}">

            <div class="relative">
                <x-utils.autocomplete
                    :items="$users->values()->toArray()"
                    inputId="user_search"
                    dropdownId="user_dropdown"
                    hiddenId="user_id"
                    label="Pseudo de l'utilisateur"
                    placeholder="Rechercher un utilisateur..."
                    itemLabel="name"
                    itemKey="id"
                />
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" class="px-6 py-3 text-gray-600 bg-gray-100/80 backdrop-blur-sm rounded-xl hover:bg-gray-200/80 transition-all duration-200 font-medium close-modal"
                    data-close-modal>
                    Annuler
                </button>
                <button type="submit" class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                    Partager
                </button>
            </div>
        </form>
    </div>
</div>