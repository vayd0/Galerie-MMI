@props(['album' => null, 'users' => null, 'selectedUserId' => null])

<div id="shareAlbumModal" class="fixed inset-0 z-50 hidden bg-black/20 backdrop-blur-sm items-center justify-center">
    <div class="relative w-screen bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-96 max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
        data-modal-content>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Partager l'album</h2>
        <p class="text-sm text-gray-500 mb-6">Sélectionnez un utilisateur pour partager cet album</p>
        <form method="POST" action="{{ route('albums.share') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="album_id" value="{{ $album->id }}">
            <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-3">
                <i class="fa-solid fa-user mr-2 text-blue"></i>
                Sélectionnez un utilisateur
            </label>
            @php
                $sortedUsers = collect($users)->sortByDesc(fn($u) => $u->id == $selectedUserId)->values();
            @endphp
            <select name="user_id" id="user_id"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200 mb-4">
                @foreach($sortedUsers as $user)
                    <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
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