@extends("templates.app")

@section('content')
    {{ print_r($albums) }}
    @foreach ($albums as $album)
        <h1>{{ $album->titre }}</h1>
        {{ print_r($album->cover) }}
        <a href="/album/{{ $album->id }}">
            <div class="album overflow-visible width-[250px]">
                <div class="invertedAlbum width-full relative overflow-visible" data-tooltip-target="tooltip-{{ $album->id }}"
                    data-tooltip-placement="top" style="background: url({{ $album->cover->url }});">
                </div>
                <button
                    class="absolute bottom-0 right-0 transform translate-x-4 translate-y-4 bg-white p-5 rounded-full shadow-lg z-10">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
            <div id="tooltip-{{ $album->id }}" role="tooltip"
                class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700">
                Tooltip on top
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </a>
    @endforeach
@endsection