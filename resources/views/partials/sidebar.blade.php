@include("components.modals.searchBar")
@if ((!Request::is('/')))
    <nav
        class="glass-morph sidebar w-[5rem] h-full bg-white p-4 my-auto text-zinc-900 flex flex-col rounded-r-2xl shadow-xl">
        <img class="mb-4 w-[4rem]" src="{{ asset('assets/logo.svg') }}" alt="Logo" />

        <ul class="grid grid-cols-1 text-[1.4rem] gap-3 text-center items-center justify-center">
            <li class="nav-item hover:scale-105 hover:rotate-[3deg] transition-all duration-300">
                <a href="/"><i class="fa-solid fa-house"></i></a>
            </li>

            <li class="nav-item hover:scale-105 hover:rotate-[3deg] transition-all duration-300">
                <a href="/albums"><i class="fa-solid fa-icons"></i></a>
            </li>

            @if ((Request::is('albums/*') || Request::is('photos/*')) && is_numeric(Request::segment(2)))
                <li class="nav-item hover:scale-105 hover:rotate-[3deg] transition-all duration-300">
                    <button class="hover:cursor-pointer" onclick="openModal('addPhotoModal')"><i
                            class="fa-solid fa-file-arrow-up"></i></button>
                </li>
            @else
                <li class="nav-item hover:scale-105 hover:rotate-[3deg] transition-all duration-300">
                    <button class="hover:cursor-pointer" onclick="openModal('addAlbumModal')"><i
                            class="fa-solid fa-folder-plus"></i></button>
                </li>
            @endif
            <li class="nav-item hover:scale-105 hover:rotate-[3deg] transition-all duration-300">
                <button class="hover:cursor-pointer" onclick="openSearch()"><i
                        class="fa-solid fa-magnifying-glass"></i></button>
            </li>
        </ul>

        <div class="flex-1"></div>
        <ul class="flex flex-col justify-center items-center gap-3">
            <li class="nav-item text-[1.4rem] hover:scale-105 hover:rotate-[-3deg] transition-all duration-300">
                <button class="hover:cursor-pointer" onclick="openModal('shareAlbumModal')">
                    <i class="fa-solid fa-users"></i>
                </button>
            </li>
            <li class="nav-item text-[1.4rem] hover:scale-105 hover:rotate-[-3deg] transition-all duration-300">
                <button class="hover:cursor-pointer">
                    <i class="fa-solid fa-gear"></i></button>
            </li>
        </ul>
    </nav>
@endif