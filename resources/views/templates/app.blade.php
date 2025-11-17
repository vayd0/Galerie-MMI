<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="text-gray-800 relative min-h-screen">
    @include("partials.loading")
    <div class="fixed inset-0 z-0">
        @include("partials.background")
    </div>
    
    <div class="relative z-10">
        @include("partials.header")
        @include("partials.navbar")
        @section('top')
        @endsection
        
        <main class="mt-[3rem] ml-[7rem] h-[80%] rounded-3xl">
            @yield('content')
        </main>
    </div>
    @vite('resources/js/animation.js')
</body>

</html>