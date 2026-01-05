@props(['items'])
<div class="masonry-grid grid-col-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 [column-gap:1rem] [row-gap:1rem]"
    style="width:100%; min-width:0; max-width:1600px; margin:0 auto;">
    @foreach($items as $item)
            <a class="break-inside-avoid block group mb-4 relative" href="{{ route('photos.show', $item->id) }}">
                <img class="w-full rounded-xl object-cover gallery-img transition-transform duration-300"
                    style="max-height: 28rem;" src="{{ $item->url }}" alt="" data-tippy-content="{{ $item->titre ?? 'Photo' }}"
                    onerror="this.onerror=null;this.src='{{ asset('assets/background.svg') }}'; this.classList.add('opacity-50');">
                @if(isset($item->note) && $item->note)
                    <div class="absolute top-2 right-2 bg-black/60 backdrop-blur-sm text-white px-2 py-1 rounded-lg text-xs flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <i class="fa-solid fa-star text-yellow-400"></i>
                        <span>{{ $item->note }}</span>
                    </div>
                @endif
            </a>
    @endforeach
</div>