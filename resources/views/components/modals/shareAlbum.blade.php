@props(['album' => null, 'users' => null, 'selectedUserId' => null])

<div id="shareAlbumModal" class="fixed inset-0 z-50 hidden bg-black/20 backdrop-blur-sm items-center justify-center">
    <div class="relative w-screen bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-96 max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
        data-modal-content>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Partager l'album</h2>
        <p class="text-sm text-gray-500 mb-6">Saisissez un pseudo pour partager cet album</p>
        <form method="POST" action="{{ route('albums.share') }}" class="space-y-6" autocomplete="off">
            @csrf
            <input type="hidden" name="album_id" value="{{ $album->id }}">
            <input type="hidden" name="user_id" id="selected_user_id" value="{{ $selectedUserId }}">
            <label for="user_search" class="block text-sm font-semibold text-gray-700 mb-3">
                <i class="fa-solid fa-user mr-2 text-blue"></i>
                Pseudo de l'utilisateur
            </label>
            <div class="relative">
                <input type="text" id="user_search" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200 mb-1"
                    placeholder="Rechercher un utilisateur..." autocomplete="off">
                <ul id="user_dropdown" class="absolute left-0 right-0 z-10 bg-white rounded-xl shadow-lg border border-gray-200 mt-1 max-h-48 overflow-y-auto hidden">
                    @foreach($users as $user)
                        <li class="user-item px-4 py-2 cursor-pointer hover:bg-blue-100 rounded flex items-center gap-2 hidden"
                            data-name="{{ strtolower($user->name) }}" data-id="{{ $user->id }}">
                            <x-utils.account :user="$user" />
                        </li>
                    @endforeach
                </ul>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('user_search');
    const dropdown = document.getElementById('user_dropdown');
    const hiddenId = document.getElementById('selected_user_id');
    const items = dropdown.querySelectorAll('.user-item');

    input.addEventListener('input', function() {
        const value = this.value.trim().toLowerCase();
        let anyVisible = false;
        items.forEach(item => {
            if (item.dataset.name.includes(value) && value.length > 0) {
                item.classList.remove('hidden');
                anyVisible = true;
            } else {
                item.classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden', !anyVisible);
        hiddenId.value = '';
    });

    items.forEach(item => {
        item.addEventListener('click', function() {
            input.value = item.dataset.name;
            hiddenId.value = item.dataset.id;
            dropdown.classList.add('hidden');
        });
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>