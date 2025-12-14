<nav class="w-full flex justify-between items-center mb-[10vh]">
    <div class=" w-[30%]">
        <img class="w-[4rem]" src="{{ asset('assets/logo.svg') }}" alt="Logo" />
    </div>
    <div class="flex justify-end gap-2 w-[30%]">
        @auth
            <div class="glass-morph py-2 px-4 text-basic" style="border-radius:50px !important;">
                <a href="{{route("logout")}}" onclick="document.getElementById('logout').submit(); return false;"><i
                        class="fa-solid fa-arrow-right-from-bracket"></i></a>
                <form id="logout" action="{{route("logout")}}" method="post">
                    @csrf
                </form>
            </div>
        @else
            <div class="glass-morph px-4 py-2 text-basic text-sm" style="border-radius:50px !important;">
                <a href="/login">Connexion</a>
            </div>
            <div class="px-4 py-2 text-zinc bg-basic text-sm" style="border-radius:50px !important;">
                <a href="/register">Inscription</a>
            </div>
        @endauth
    </div>
    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-10">
        <div id="indicators" class="flex justify-center gap-2 mb-3 mx-auto"></div>
        <div class="flex justify-center items-center gap-[2rem] glass-morph px-8 py-2 text-basic text-sm"
            style="border-radius:50px !important;">
            <a class="px-4 py-2 glass-morph" style="border-radius:100px; !important" href="/"><i
                    class="fa-solid fa-house"></i></a>
            @auth
                <a href="/albums"><i class="fa-solid fa-icons"></i></a>
            @endauth
        </div>
    </div>
</nav>