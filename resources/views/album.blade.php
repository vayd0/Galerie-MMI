@extends("templates.app")

@section('content')
<section class="flex flex-wrap justify-center md:justify-start gap-3 w-full">
    @foreach ($albums as $album)
        <div class="relative py-1 px-2 bg-gray-50 rounded-[2rem] hover:cursor-pointer transition-all duration-300 hover:scale-102" id="album">
            <a href="/album/{{ $album->id }}">
                <div class="album relative overflow-visible">
                    <div class="invertedAlbum relative overflow-visible" data-tooltip-target="tooltip-{{ $album->id }}"
                        data-tooltip-placement="top" style="background: url({{ $album->cover->url }});">
                    </div>
                    <div class="absolute bottom-[1rem] right-[0.75rem] transform translate-x-4 translate-y-4 p-2 rounded-full z-10 rotate-[-15deg] transition-all duration-300" id="arrow">
                        <img class="w-[3rem]" src="{{ asset('assets/arrow.svg') }}" alt="arrow"/>
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
