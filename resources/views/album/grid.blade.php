@extends("templates.app")
@include("components.modals.addAlbumModal")
@section('content')
    <div class="fixed inset-0 z-0">
        @include("components.utils.background")
    </div>
    <section
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 w-full p-4 pr-8 max-h-[80vh] overflow-y-auto">
        <div class="relative p-1 bg-darkblue rounded-xl hover:cursor-pointer transition-all duration-300 hover:scale-102">
            <button type="button" onclick="openModal('addAlbumModal')" class="w-full">
                <div class="bg-darkblue w-full h-[15rem] sm:h-full flex justify-center items-center border border-darkblue rounded-xl hover:bg-basic hover:border-3 transition-all duration-300 text-basic hover:text-blue"
                    id="add-album">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </button>
        </div>

        @foreach ($albums as $album)
            <div class="relative p-1 bg-darkblue rounded-xl hover:cursor-pointer transition-all duration-300 hover:scale-102"
                id="album">
                <a href="/album/{{ $album->id }}">
                    <div class="album relative overflow-visible">

                        <img class="invertedAlbum relative overflow-visible w-full h-[15rem] object-cover rounded-xl"
                            data-tooltip-target="tooltip-{{ $album->id }}" data-tooltip-placement="top"
                            src="{{ $album->cover }}" alt="cover"
                            onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');" />
                        <div class="absolute bottom-[0.4rem] right-[0.3rem] transform translate-x-4 translate-y-4 p-2 rounded-full z-10 rotate-[-15deg] transition-all duration-300"
                            id="arrow">
                            <img class="w-[2.5rem] brightness-0 invert" src="{{ asset('assets/arrow.svg') }}" alt="arrow" />
                        </div>
                    </div>
                </a>
                <div id="tooltip-{{ $album->id }}" role="tooltip"
                    class="absolute z-10 invisible inline-block px-1 py-1 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                    Tooltip on top
                    <div class="tooltip-arrow" data-popper-arrow></div>
                </div>
            </div>
        @endforeach
    </section>
@endsection