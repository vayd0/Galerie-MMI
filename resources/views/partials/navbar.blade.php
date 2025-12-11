<nav class="w-full flex justify-between items-center px-2">
    <div>
        <img class="w-[4rem]" src="{{ asset('assets/logo.svg') }}" alt="Logo" />
    </div>
    <div class="flex justify-center items-center gap-[2rem] glass-morph py-3 px-8 text-basic"
        style="border-radius:50px !important;">
        <a href="/">Accueil</a>
        @auth
            <a href="/albums">Albums</a>
        @endauth
    </div>
    <div>
        @auth
            <div class="glass-morph py-3 px-4 text-basic" style="border-radius:50px !important;">
                <a href="{{route("logout")}}" onclick="document.getElementById('logout').submit(); return false;"><i
                        class="fa-solid fa-arrow-right-from-bracket"></i></a>
                <form id="logout" action="{{route("logout")}}" method="post">
                    @csrf
                </form>
            </div>
        @else
            <div class="glass-morph p-2">
                <a href="/login">Connexion</a>
            </div>
            <a href="/register">Inscription</a>
        @endauth
    </div>
</nav>