@extends("templates.app")
@section('content')
    @include("components.modals.addPhotoModal")
    
    <div
        class="h-full w-full glass-morph overflow-y-auto overflow-x-hidden grid-col-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 p-4 [column-gap:1rem] [row-gap:1rem]">
        <div class="break-inside-avoid mb-4">
            <button
                class="w-full h-[15rem] bg-basic flex justify-center items-center border border-3 border-blue rounded-xl hover:bg-blue hover:border-basic transition-all duration-300 text-blue hover:text-basic"
                id="add-photo" onclick="openModal('addPhotoModal')">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        @foreach($photos as $photo)
            <a class="break-inside-avoid block mb-4" href="{{ route('photos.show', ['id' => $photo->id]) }}">
                <img class="w-full rounded-xl transition-all duration-300 object-cover" style="max-height: 28rem;"
                    src="{{ $photo->url }}" alt="" id="grid-img"
                    onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');">
            </a>
        @endforeach
    </div>
@endsection