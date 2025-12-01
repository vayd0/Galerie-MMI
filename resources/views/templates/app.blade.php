<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    @vite('resources/css/app.css')
</head>

<body class="text-gray-800 relative min-h-screen">
    @include("components.utils.loading")

    <div class="relative z-10">
        <div class="mr-[3rem]">
            @include("partials.header")
        </div>
        @include("partials.navbar")
        @section('top')
        @endsection

        <main class="mt-[0.2rem] ml-[7rem] mr-[3rem] h-[80%] rounded-3xl">
            @yield('content')
        </main>
    </div>
    @vite('resources/js/animation.js')
    @vite('resources/js/modal.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://assets.codepen.io/16327/MorphSVGPlugin3.min.js"></script>
    @stack('tooltip.scripts')
</body>

</html>