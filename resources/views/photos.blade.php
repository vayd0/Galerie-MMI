@extends("templates.app")

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 auto-rows-fr">
            <div class="bg-blue flex justify-center items-center border border-blue rounded-xl hover:bg-basic hover:border-3 transition-all duration-300 text-basic hover:text-blue" id="add-photo">
                <i class="fa-solid fa-plus"></i>
            </div>
        @foreach($photos as $index => $photo)
            @php
                $randomNumber = rand(0, 4);

                $isLarge = $randomNumber == 0;
                $gridClasses = $isLarge ? 'col-span-2 row-span-2' : '';
            @endphp

            <div class="{{ $gridClasses }}">
                <img class="h-full w-full object-cover rounded-xl transition-all duration-300" id="grid-img" src="{{ $photo->url }}" alt="">
            </div>
        @endforeach
    </div>
@endsection