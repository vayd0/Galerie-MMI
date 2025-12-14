@extends("templates.app")
@section('content')
<x-modals.shareAlbum :album="$album" :users="$users" :selectedUserId="auth()->id()" />
    <x-modals.addPhotoModal :album-id="$album->id" :tags="$tags" />
    <x-modals.filterPhotosModal :tags="$tags" :users="$users" />
    <div class="w-full h-[10%] glass-morph flex justify-between items-center p-2 mb-4">
        <button
            class="glass-morph w-[3rem] h-[3rem] flex justify-center items-center rounded-xl transition-all duration-300 text-black"
            id="filter-photo" onclick="openModal('filterPhotosModal')">
            <i class="fa-solid fa-filter"></i>
        </button>
        <button
            class="glass-morph w-[3rem] h-[3rem] text-[2rem] flex justify-center items-center rounded-xl transition-all duration-300 text-black"
            id="add-photo" onclick="openModal('addPhotoModal')">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
    <div class="w-full overflow-y-auto overflow-hidden glass-morph h-[88%]">
        <div class="px-[1.5rem] py-[2rem]">
            @if($photos->isEmpty())
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 text-xl py-16">
                    <span>Pss, ajoutez votre première photo en cliquant sur le +...</span>
                </div>
            @else
                <x-masonry-grid :items="$photos" />
            @endif
        </div>
    </div>
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