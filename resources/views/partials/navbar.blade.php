<nav>
    @auth
        <a href="{{route("logout")}}"
           onclick="document.getElementById('logout').submit(); return false;">Logout</a>
        <form id="logout" action="{{route("logout")}}" method="post">
            @csrf
        </form>
    @else
        <a href="/login">connexion</a>
        <a href="/register">register</a>
        @endauth
</nav>