<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
</head>

<body class="text-gray-800">
    @include("partials.header")
    @include("partials.navbar")
    @section('top')

    @endsection
    <main class="m-[3rem] mx-[7rem] p-7 h-[80%] rounded-3xl">
        @yield('content')
    </main>
</body>

</html>