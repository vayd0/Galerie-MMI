@props([])
<div id="notificationsModal"
    class="fixed inset-0 hidden items-center justify-center z-50 transition-all duration-300 ease-in-out w-full">
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
    <div class="relative bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8 w-1/2 mx-4 transform transition-all duration-300 ease-out scale-95 opacity-0 bg-basic/20"
        data-modal-content>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-darkblue">Notifications</h2>
            <button onclick="closeModal('notificationsModal')"
                class="ms-auto flex items-center justify-center text-gray-400 hover:text-gray-700 bg-transparent border border-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded h-8 w-8 focus:outline-none transition">
                <span class="sr-only">Fermer</span>
                <svg class="w-5 h-5" aria-hidden="true" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </button>
        </div>
        <div class="space-y-3 max-h-[60vh] overflow-y-auto">
            @php
                $notifications = auth()->check() ? auth()->user()->notifications()->orderByDesc('created_at')->get() : collect();
            @endphp
            @forelse($notifications as $notif)
                <div class="flex items-center w-full max-w-full p-4 bg-basic rounded-base border-1 border-gray-200 shadow-xs rounded-xl"
                    role="alert">
                    <svg class="w-6 h-6 {{ $notif->type === 'success' ? 'text-green-500' : 'text-blue-500' }}"
                        aria-hidden="true" fill="none" viewBox="0 0 24 24">
                        @if($notif->type === 'success')
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        @else
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 20v-6m0-4h.01M12 4a8 8 0 1 1 0 16 8 8 0 0 1 0-16Z" />
                        @endif
                    </svg>
                    <div class="ms-2.5 text-sm border-s border-gray-200 ps-3.5 flex-1">
                        <div class="flex justify-between items-start">
                            <div class="font-semibold">{{ $notif->title }}</div>
                            <div class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                        <p>{{ $notif->message }}</p>
                    </div>
                </div>
            @empty
                <div class="text-gray-500 text-center py-8">Aucune notification.</div>
            @endforelse
        </div>
    </div>
</div>