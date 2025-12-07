<article class="fixed inset-0 h-full w-full z-[999] flex items-center justify-center hidden opacity-0"
    id="searchBarContainer">
    <div class="absolute inset-0 backdrop-blur-sm bg-black/30"></div>

    <div class="glass-morph w-1/2 p-6 flex flex-col" id="searchForm">
        <div class="w-full mx-3" id="searchBar">
            <div class="flex justify-between items-center">
                <input type="text" class="w-full text-white focus:outline-0 active:outline-0"
                    placeholder="Rechercher...">
                <a href="" class="text-white mr-3" id="searchBtn"><i class="fa-solid fa-magnifying-glass"></i></a>
            </div>
        </div>

        <div class="w-full max-h-[40vh] overflow-y-auto mx-3" id="searchBarContent"></div>
    </div>
</article>