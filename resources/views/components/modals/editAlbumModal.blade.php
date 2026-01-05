@props(['album' => null, 'tags' => []])
<div id="editAlbumModal" class="fixed inset-0 hidden items-center justify-center z-50 transition-all duration-300 ease-in-out">
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
    
    <div class="relative bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-96 max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0" data-modal-content>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Modifier l'album</h2>
                <p class="text-sm text-gray-500 mt-1">Modifiez le titre de votre album</p>
            </div>
            <button data-close-modal class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-colors duration-200">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <form action="" method="POST" class="space-y-6" id="editAlbumForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="editAlbumId" name="album_id" value="{{ $album?->id }}">
            
            <div>
                <label for="editAlbumTitreAlbum" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fa-solid fa-image mr-2 text-blue"></i>
                    Titre de l'album
                </label>
                <input type="text" id="editAlbumTitreAlbum" name="titre" required
                    placeholder="Entrez le nom de votre album..."
                    value="{{ $album?->titre }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" data-close-modal
                    class="px-6 py-3 text-gray-600 bg-gray-100/80 backdrop-blur-sm rounded-xl hover:bg-gray-200/80 transition-all duration-200 font-medium">
                    Annuler
                </button>
                <button type="submit" 
                    class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fa-solid fa-save mr-2"></i>
                    Modifier l'album
                </button>
            </div>
        </form>
    </div>
</div>