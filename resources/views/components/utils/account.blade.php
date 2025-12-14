@props(['user'])

<div class="flex items-center gap-3">
    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold text-lg uppercase select-none">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <span class="text-base font-medium text-gray-800">{{ $user->name }}</span>
</div>