<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    @vite('resources/js/animation.js');
    @vite('resources/js/modal.js');
</body>

</html>