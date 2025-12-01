@extends("templates.app")
@include("components.modals.addAlbumModal")
@section('content')
    <div class="fixed inset-0 z-0">
        @include("components.utils.background")
    </div>
    <section
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-10 w-full p-6 pr-8 mt-10 max-h-[90vh] overflow-y-auto overflow-x-hidden">
        <div class="relative p-1 bg-darkblue rounded-xl hover:cursor-pointer transition-all duration-300 hover:scale-102">
            <button type="button" onclick="openModal('addAlbumModal')" class="w-full">
                <div class="bg-darkblue w-full h-[15rem] sm:h-full flex justify-center items-center border border-darkblue rounded-xl hover:bg-basic hover:border-3 transition-all duration-300 text-basic hover:text-blue"
                    id="add-album">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </button>
        </div>

        @foreach ($albums as $album)
            <div class="relative p-1 bg-darkblue rounded-2xl hover:cursor-pointer transition-all duration-300 hover:scale-95 group"
                id="album">
                <a href="/album/{{ $album->id }}">
                    <div class="album relative overflow-visible">
                        <img class="invertedAlbum relative overflow-visible w-full h-[15rem] object-cover rounded-xl"
                            data-tippy-content="{{ $album->titre }}" src="{{ $album->cover }}" alt="cover"
                            onerror="this.onerror=null;this.src='{{ asset('assets/error.svg') }}'; this.classList.add('bg-basic');" />
                        <div class="absolute bottom-[0.4rem] right-[0.3rem] transform translate-x-4 translate-y-4 p-2 rounded-full z-10 rotate-[-15deg] transition-all duration-300"
                            id="arrow">
                            <img class="w-[4.5rem] sm:w-[2.5rem] md:w-[3vw] brightness-0 invert"
                                src="{{ asset('assets/arrow.svg') }}" alt="arrow" />
                        </div>
                    </div>
                </a>
                <div
                    class="absolute left-1/2 bottom-0 -translate-x-1/2 w-[50%] md:w-[70%] h-11 bg-darkblue rounded-b-lg z-[-10] transition-transform duration-300 translate-y-0 group-hover:translate-y-10 p-1 flex items-center justify-center space-x-6">
                    <button type="button" class="text-basic hover:text-lime hover:cursor-pointer hover:scale-110 transition-transform duration-200" title="Éditer">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <form action="{{ route('albums.delete', $album->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-basic hover:text-red-500 hover:cursor-pointer hover:scale-110 transition-transform duration-200" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cet album ?');">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </section>
@endsection
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