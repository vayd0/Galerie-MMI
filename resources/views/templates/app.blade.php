<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squish</title>
    <link rel="icon" type="image/svg+xml" href="/assets/logo.svg">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    @vite('resources/css/app.css')
</head>

<body class="text-gray-800 relative min-h-screen overflow-y-scroll md:overflow-hidden">


    <div class="w-full h-full flex justify-center items-center p-2">
        <x-utils.toast />
        <div class="relative z-10 flex justify-between m-auto h-[90vh] w-[95vw] gap-4">
            @include("partials.sidebar")
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
    @vite('resources/js/search.js')
    @vite('resources/js/animation.js')
    @vite('resources/js/script.js')
    @stack('scripts')
    @stack('tooltip.scripts')
</body>

</html>