@extends("templates.app")

@section('content')
    @include("components.modals.addPhotoModal", ['albumId' => $photo->album_id ?? null])
    <div class="w-full h-full mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-full">
            <div class="glass-morph p-4 h-full">
                <a href="{{ url()->previous() }}" class="text-blue hover:underline flex items-center mb-4">
                    <img class="mr-1" src="{{ asset("assets/arrow.svg") }}"
                        style="width:1.5rem;transform:rotate(-155deg);" />
                    Retour à l'album
                </a>
                <h1 class="text-2xl font-bold mb-2">{{ $photo->titre }}</h1>
                <div class="relative rounded-xl overflow-hidden mb-6">
                    <img id="main-photo" src="{{ $photo->url }}" alt="{{ $photo->titre }}"
                        class="w-full object-cover max-h-[50vh]"
                        onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');">
                    <button id="fullscreen-btn" type="button" title="Plein écran"
                        class="absolute top-2 right-2 bg-white/80 hover:bg-white rounded-full p-2 shadow-md transition"
                        style="z-index:10;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="#222" stroke-width="2" d="M4 8V4h4m8 0h4v4m0 8v4h-4m-8 0H4v-4" />
                        </svg>
                    </button>
                    <div id="mini-modal"
                        class="hidden fixed top-[-2.22rem] left-[-7.725rem] inset-0 z-50 flex items-center justify-center w-[110vw] h-screen bg-black/60">
                        <div class="relative glass-morph rounded-lg p-4 flex flex-col items-center">
                            <button id="close-modal"
                                class="absolute top-0 right-2 text-basic text-2xl font-bold">&times;</button>
                            <img src="{{ $photo->url }}" alt="{{ $photo->titre }}" class="object-contain rounded-xl"
                                style="max-width:100vw; max-height:80vh;">
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-4 text-1xl">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= $photo->note ? 'text-light-lime' : 'text-gray-300' }}"></i>
                    @endfor
                </div>
                <div>
                    @if($photo->tags && count($photo->tags))
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($photo->tags as $tag)
                                <span class="glass-morph text-darkblue px-3 py-1 rounded-xl">{{ $tag->nom }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="glass-morph p-4 h-full overflow-y-auto z-[-1]">
                <div class="columns-1 sm:columns-3 gap-4 space-y-4">
                    @foreach($photos as $photo)
                        <a class="mb-4 break-inside-avoid block" href="{{ route('photos.show', $photo->id) }}">
                            <div class="aspect-square w-full rounded-xl overflow-hidden mb-4">
                                <img class="w-full h-full object-cover transition-all duration-300" src="{{ $photo->url }}"
                                    alt="" id="grid-img"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');">
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection