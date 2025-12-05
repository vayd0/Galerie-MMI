@extends("templates.app")

@section('content')
    @include("components.modals.addPhotoModal")
    
    <div class="w-full h-full mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-full">
            <div class="glass-morph p-4 h-full">
                <a href="{{ url()->previous() }}" class="text-blue hover:underline flex items-center mb-4">
                    <img class="mr-1" src="{{ asset("assets/arrow.svg") }}"
                        style="width:1.5rem;transform:rotate(-155deg);" />
                    Retour à l'album
                </a>
                <h1 class="text-2xl font-bold mb-2">{{ $photo->titre }}</h1>
                <div class="rounded-xl overflow-hidden shadow-lg mb-6">
                    <img src="{{ $photo->url }}" alt="{{ $photo->titre }}" class="w-full object-cover max-h-[32rem]"
                        onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');">
                </div>
                <div class="flex items-center gap-2 mb-4 text-1xl">
                    <span class="font-semibold">Note :</span>
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= $photo->note ? 'text-light-lime' : 'text-gray-300' }}"></i>
                    @endfor
                </div>
            </div>
            <div class="glass-morph p-4 h-full overflow-y-auto">
                <div class="columns-1 sm:columns-2 gap-4 space-y-4">
                    @foreach($photos as $photo)
                        <a class="mb-4 break-inside-avoid block" href="{{ route('photos.show', ['id' => $photo->id]) }}">
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