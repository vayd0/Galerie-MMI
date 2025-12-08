@extends("templates.app")
@section('content')
    @include("components.utils.loading")
    @include("partials.header")
    <x-add-album-modal />

    <section class="h-full m-auto">
        <div class="h-[90%] glass-morph overflow-hidden overflow-y-auto">
            <section id="section-albums">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10 w-full mt-2 py-[3rem] px-[1.5rem] max-h-[90vh] overflow-y-auto overflow-x-hidden">
                    <div
                        class="relative p-1 bg-darkblue rounded-xl hover:cursor-pointer transition-all duration-300 hover:scale-102">
                        <button type="button" onclick="openModal('addAlbumModal')" class="w-full h-full">
                            <div class="bg-darkblue w-full h-[15rem] sm:h-[100px] flex justify-center items-center border border-darkblue rounded-xl hover:bg-basic hover:border-3 transition-all duration-300 text-basic hover:text-blue"
                                id="add-album">
                                <i class="fa-solid fa-plus"></i>
                            </div>
                        </button>
                    </div>

                    @foreach ($albums as $album)
                        <div class="relative p-1 bg-darkblue rounded-2xl hover:cursor-pointer transition-all duration-300 hover:scale-95 group"
                            id="album">
                            <a href="/albums/{{ $album->id }}">
                                <div class="album relative overflow-visible">
                                    <img class="invertedAlbum relative overflow-visible w-full h-[8rem] object-cover rounded-xl"
                                        data-tippy-content="{{ $album->titre }}"
                                        src="{{ $album->cover }}" alt="cover"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/error.svg') }}'; this.classList.add('bg-basic');" />
                                    <div class="absolute bottom-[0.2rem] right-[0.2rem] transform translate-x-2 translate-y-2 p-1 rounded-full z-10 rotate-[-15deg] transition-all duration-300"
                                        id="arrow">
                                        <img class="w-[2.5rem] md:w-[3.9vw] lg:w-[2.5rem] brightness-0 invert"
                                            src="{{ asset('assets/arrow.svg') }}" alt="arrow" />
                                    </div>
                                </div>
                            </a>
                            <div
                                class="absolute left-1/2 bottom-1 -translate-x-1/2 w-[60%] md:w-[80%] h-8 bg-darkblue rounded-b-lg z-[-10] transition-transform duration-300 translate-y-0 group-hover:translate-y-8 p-1 flex items-center justify-center space-x-4">
                                <button type="button"
                                    class="text-basic hover:text-lime hover:cursor-pointer hover:scale-110 transition-transform duration-200"
                                    title="Éditer">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('albums.delete', $album->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-basic hover:text-red-500 hover:cursor-pointer hover:scale-110 transition-transform duration-200"
                                        title="Supprimer"
                                        onclick="return confirm('Voulez-vous vraiment supprimer cet album ?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
           <section id="section-photos" class="py-[3rem] px-[1.5rem]" style="display:none">
                <x-masonry-grid :items="$photos" />
            </section>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navAlbums = document.getElementById('nav-albums');
            const navPhotos = document.getElementById('nav-photos');
            const navSlider = document.getElementById('nav-slider');
            const navCheckbox = document.getElementById('nav-checkbox');

            const sectionAlbums = document.getElementById('section-albums');
            const sectionPhotos = document.getElementById('section-photos');

            function showAlbums() {
                navSlider.style.transform = 'translateX(0)';
                navCheckbox.checked = false;
                navAlbums.classList.add('text-darkblue');
                navPhotos.classList.remove('text-darkblue');
                if (sectionAlbums) sectionAlbums.style.display = 'block';
                if (sectionPhotos) sectionPhotos.style.display = 'none';
            }
            function showPhotos() {
                navSlider.style.transform = 'translateX(110%)';
                navCheckbox.checked = true;
                navPhotos.classList.add('text-darkblue');
                navAlbums.classList.remove('text-darkblue');
                if (sectionAlbums) sectionAlbums.style.display = 'none';
                if (sectionPhotos) sectionPhotos.style.display = 'block';
            }

            navAlbums.addEventListener('click', showAlbums);
            navPhotos.addEventListener('click', showPhotos);

            showAlbums();
        });
    </script>
@endpush
@push('tooltip.scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tippy('[data-tippy-content]', {
                placement: 'top',
                animation: 'perspective',
                interactive: 'true',
                arrow: false
            });
        });
    </script>
@endpush