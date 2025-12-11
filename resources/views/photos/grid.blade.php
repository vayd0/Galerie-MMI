@extends("templates.app")
@section('content')
    <x-modals.addPhotoModal :album-id="$album->id" :tags="$tags" />
    <x-modals.shareAlbum :album="$album" :users="$users" :selectedUserId="auth()->id()" />
    <div class="w-full h-[10%] glass-morph flex justify-between items-center p-2 mb-4">
        <button
            class="glass-morph w-[3rem] h-[3rem] flex justify-center items-center rounded-xl transition-all duration-300 text-black"
            id="filter-photo" onclick="openModal('addPhotoModal')">
            <i class="fa-solid fa-filter"></i>
        </button>
        <button
            class="glass-morph w-[3rem] h-[3rem] text-[2rem] flex justify-center items-center rounded-xl transition-all duration-300 text-black"
            id="add-photo" onclick="openModal('addPhotoModal')">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
    <div class="w-full overflow-y-auto overflow-hidden glass-morph h-[88%]">
        <div class=" px-[1.5rem] py-[2rem]">
            <x-masonry-grid :items="$photos" />
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