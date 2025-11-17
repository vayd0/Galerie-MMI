@extends("templates.app")

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 auto-rows-fr">
        @foreach($photos as $index => $photo)
            @php
                $randomNumber = rand(0, 4);
                // Image grande si randomNumber == 0 (20% de chance)
                $isLarge = $randomNumber == 0;
                $gridClasses = $isLarge ? 'col-span-2 row-span-2' : '';
            @endphp
            
            <div class="{{ $gridClasses }}">
                <img class="h-full w-full object-cover rounded-xl" 
                     src="{{ $photo->url }}" 
                     alt="">
            </div>
        @endforeach
    </div>
@endsection