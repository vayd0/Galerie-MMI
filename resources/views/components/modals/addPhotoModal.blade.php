@props(['albumId' => null, 'tags' => []])
<div id="addPhotoModal"
    class="fixed inset-0 hidden items-center justify-center z-50 transition-all duration-300 ease-in-out">
    <div class="absolute w-screen inset-0 bg-black/20 backdrop-blur-sm flex justify-center items-center">

        <div class="relative w-screen bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-96 max-w-md mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0"
            data-modal-content>
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Nouvelle photo</h2>
                    <p class="text-sm text-gray-500 mt-1">Ajoutez une photo à votre album</p>
                </div>
                <button data-close-modal
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-colors duration-200">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('photos.add') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="album_id" value="{{ $albumId }}">
                <input type="hidden" id="noteValue" name="note" value="3">

                <div>
                    <label for="titre" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-image mr-2 text-blue"></i>
                        Titre de la photo
                    </label>
                    <input type="text" id="titre" name="titre" required placeholder="Entrez le titre de votre photo..."
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                </div>

                <div>
                    <label for="url" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-link mr-2 text-blue"></i>
                        URL de la photo
                    </label>
                    <div class="flex gap-2 items-center">
                        <input type="url" id="url" name="url" required placeholder="https://exemple.com/photo.jpg"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                        <label for="fileInput"
                            class="cursor-pointer p-4 bg-blue rounded-xl hover:bg-darkblue transition-colors duration-200 flex items-center">
                            <i class="fa-solid fa-upload text-basic"></i>
                            <input type="file" id="fileInput" name="photo_file"
                                accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" class="hidden">
                        </label>
                    </div>
                </div>

                <div>
                    <label for="tagInput" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-tags mr-2 text-blue"></i>
                        Tags
                    </label>
                    <div class="relative">
                        <div class="flex gap-2 items-center mb-2">
                            <input type="text" id="tagInput"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200"
                                placeholder="Rechercher ou ajouter un tag...">
                            <button type="button" id="addTagBtn"
                                class="p-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-colors duration-200 flex items-center"
                                title="Ajouter le tag">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <div id="tagDropdown"
                            class="absolute min-h-[3rem] max-h-[10rem] left-0 bg-white overflow-y-scroll border border-gray-200 rounded-xl shadow-lg mt-1 w-full z-10 hidden">
                        </div>
                    </div>
                    <div id="selectedTags" class="flex flex-wrap gap-2 mt-2"></div>
                    <input type="hidden" name="tags" id="tagsHidden">
                    <small class="text-gray-500">Vous pouvez rechercher, sélectionner ou ajouter des tags</small>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fa-solid fa-star mr-2 text-blue"></i>
                        Note
                    </label>
                    <div class="flex gap-2" id="starRating">
                        <i class="fa-solid fa-star text-2xl text-light-lime cursor-pointer transition-colors duration-200 hover:scale-110"
                            data-rating="1"></i>
                        <i class="fa-solid fa-star text-2xl text-light-lime cursor-pointer transition-colors duration-200 hover:scale-110"
                            data-rating="2"></i>
                        <i class="fa-solid fa-star text-2xl text-light-lime cursor-pointer transition-colors duration-200 hover:scale-110"
                            data-rating="3"></i>
                        <i class="fa-solid fa-star text-2xl text-gray-300 cursor-pointer transition-colors duration-200 hover:scale-110"
                            data-rating="4"></i>
                        <i class="fa-solid fa-star text-2xl text-gray-300 cursor-pointer transition-colors duration-200 hover:scale-110"
                            data-rating="5"></i>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" data-close-modal
                        class="px-6 py-3 text-gray-600 bg-gray-100/80 backdrop-blur-sm rounded-xl hover:bg-gray-200/80 transition-all duration-200 font-medium">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Ajouter la photo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    window.tagsFromBlade = @json($tags);
    window.csrfToken = '{{ csrf_token() }}';
</script>