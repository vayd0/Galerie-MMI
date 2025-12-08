@extends("templates.app")
@section('content')
    @include("components.modals.addPhotoModal")
    <script>
        window.tagsFromBlade = @json($tags);
    </script>

    <div class="h-full w-full glass-morph overflow-y-auto overflow-x-hidden p-4">
        <div class="mb-4">
            <button
                class="w-full h-[7.5rem] bg-basic flex justify-center items-center border border-3 border-blue rounded-xl hover:bg-blue hover:border-basic transition-all duration-300 text-blue hover:text-basic"
                id="add-photo" onclick="openModal('addPhotoModal')">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        <x-masonry-grid :items="$photos" type="photo" />
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