@extends("templates.app")

@section('content')
<section class="flex flex-wrap justify-center md:justify-start gap-3 w-full">
    <div class="relative py-1 px-2 bg-gray-50 rounded-[2rem] hover:cursor-pointer transition-all duration-300 hover:scale-102 hover:cursor-pointer">
    <div class="bg-blue w-[15rem] h-[11rem] flex justify-center items-center border border-blue rounded-2xl hover:bg-basic hover:border-3 transition-all duration-300 text-basic hover:text-blue" id="add-album">
        <i class="fa-solid fa-plus"></i>
    </div></div>
    @foreach ($albums as $album)
        <div class="relative py-1 px-2 bg-gray-50 rounded-[2rem] hover:cursor-pointer transition-all duration-300 hover:scale-102 hover:cursor-pointer" id="album">
            <a href="/album/{{ $album->id }}">
                <div class="album relative overflow-visible">
                    <div class="invertedAlbum relative overflow-visible" data-tooltip-target="tooltip-{{ $album->id }}"
                        data-tooltip-placement="top" style="background: url({{ $album->cover }});">
                    </div>
                    <div class="absolute bottom-[0.75rem] right-[0.5rem] transform translate-x-4 translate-y-4 p-2 rounded-full z-10 rotate-[-15deg] transition-all duration-300" id="arrow">
                        <img class="w-[2.5rem]" src="{{ asset('assets/arrow.svg') }}" alt="arrow"/>
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