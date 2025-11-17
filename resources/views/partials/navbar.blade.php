<nav class="sidebar fixed top-0 w-[5rem] h-[100vh] bg-white p-4 text-zinc-900 flex flex-col rounded-r-2xl shadow-xl">
    <img class="mb-4" src="{{ asset('assets/logo.svg') }}" alt="Logo" />

    <ul class="grid grid-cols-1 text-[1.4rem] gap-3 text-center">
        <li class="nav-item">
            <a href="/"><i class="fa-solid fa-house"></i></a>
        </li>
        <li class="nav-item">
            <button><i class="fa-solid fa-folder-plus"></i></button>
        </li>
        <li class="nav-item">
            <button><i class="fa-solid fa-user"></i></button>
        </li>
        <li class="nav-item">
            <button><i class="fa-solid fa-magnifying-glass"></i></button>
        </li>
    </ul>

    <div class="flex-1"></div>

    <div class="nav-item text-[1.4rem] text-center">
        <i class="fa-solid fa-gear"></i>
    </div>
</nav>