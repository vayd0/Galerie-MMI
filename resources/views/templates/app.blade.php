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

<body class="text-gray-800 relative min-h-screen overflow-y-scroll md:overflow-hidden">

    <div class="w-full h-full flex justify-center items-center p-2">
        <div class="relative z-10 flex justify-between m-auto h-[90vh] w-[95vw] gap-4">
            @include("partials.navbar")
            @section('top')
            @endsection

            <main class="rounded-3xl w-full">
                <div class="fixed inset-0 z-[-30]">
                    @include("components.utils.background")
                </div>
                @yield('content')
            </main>
        </div>
    </div>
    @vite('resources/js/modal.js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    @stack('tooltip.scripts')
</body>

</html>