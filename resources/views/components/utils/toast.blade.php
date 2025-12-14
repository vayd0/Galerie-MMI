@php
    $toast = session('toast');
    if (!$toast && $errors->any()) {
        $toast = [
            'type' => 'error',
            'message' => implode(' | ', $errors->all()),
        ];
    }
@endphp

@if($toast)
    @php
        $color = $toast['type'] === 'success'
            ? 'bg-green-100 text-green-800 border-green-300'
            : 'bg-red-100 text-red-800 border-red-300';
        $icon = $toast['type'] === 'success'
            ? 'fa-circle-check'
            : 'fa-circle-exclamation';
    @endphp
    <div class="toast-gsap fixed bottom-6 right-6 z-[9999] flex items-center gap-2 border {{ $color }} px-4 py-3 rounded-xl mb-4 shadow transition-all duration-300">
        <i class="fa-solid {{ $icon }}"></i>
        <span>{!! $toast['message'] !!}</span>
    </div>
@endif