<article class="fixed inset-0 h-full w-full z-[999] flex items-center justify-center hidden opacity-0"
    id="searchBarContainer">
    <div class="absolute inset-0 backdrop-blur-sm bg-black/30"></div>

    <div class="glass-morph w-1/2 p-6 flex flex-col gap-[2rem]" id="searchForm">
        <div class="mt-2 w-full mx-3" id="searchBar">
            <div class="flex justify-between items-center">
                <input type="text" class="w-full text-white focus:outline-0 active:outline-0"
                    placeholder="Rechercher...">
                <a href="" class="text-white mr-3" id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></a>
            </div>
        </div>

        <div class="w-full max-h-[40vh] overflow-y-auto mx-3" id="searchBarContent">
            @if (isset($photos))
                @foreach($photos as $photo)
                    <div class="w-full flex items-center justify-between py-4">
                        <div class="flex items-center gap-6">
                            <img class="h-[2rem] aspect-square object-cover rounded" src="{{ asset($photo->url) }}"
                                alt="{{ $photo->titre }}">
                            <h1 class="text-white">{{ $photo->titre }}</h1>
                            <div class="tags">
                                <div>
                                    @if (isset($photos->tags))
                                        @foreach ($photos->tags as $tag)

                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="mr-3 flex items-center">
                            <h1 class="text-white">{{ $photo->username || "Inconnu" }}</h1>
                        </div>
                    </div>
                @endforeach
            @elseif (isset($albums))
                @foreach($albums as $album)
                    <div class="w-full flex items-center justify-between py-4">
                        <div class="flex items-center gap-6">
                            <img class="h-[2rem] aspect-square object-cover rounded" src="{{ asset($album->cover) }}"
                                alt="{{ $album->titre }}">
                            <h1 class="text-white">{{ $album->titre }}</h1>
                        </div>
                        <div class="mr-3 mx-auto flex items-center">
                            <h1 class="text-white">{{ $album->username || "Inconnu" }}</h1>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</article>