@props([])

<div id="filterAlbumsModal"
    class="fixed inset-0 hidden items-center justify-center z-50 transition-all duration-300 ease-in-out w-full">
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
    <div class="relative bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-1/2 mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
        data-modal-content>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-darkblue">Filtrer les albums</h2>
            <button onclick="closeModal('filterAlbumsModal')"
                class="ms-auto flex items-center justify-center text-gray-400 hover:text-gray-700 bg-transparent border border-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded h-8 w-8 focus:outline-none transition">
                <span class="sr-only">Fermer</span>
                <svg class="w-5 h-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>
        <form method="GET" action="" class="space-y-6">
            <div>
                <label for="sort_by" class="block text-sm font-semibold text-gray-700 mb-2">Trier par</label>
                <select id="sort_by" name="sort_by" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue transition-all duration-200">
                    <option value="creation" {{ request('sort_by') == 'creation' ? 'selected' : '' }}>Date de création</option>
                    <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>ID</option>
                    <option value="titre" {{ request('sort_by') == 'titre' ? 'selected' : '' }}>Titre</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal('filterAlbumsModal')" class="px-6 py-3 text-gray-600 bg-gray-100/80 rounded-xl hover:bg-gray-200/80 transition-all duration-200 font-medium">
                    Annuler
                </button>
                <button type="submit" class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                    Trier
                </button>
            </div>
        </form>
    </div>
</div>