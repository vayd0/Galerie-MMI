@props([
    'items' => [],
    'inputId' => 'autocomplete_input',
    'dropdownId' => 'autocomplete_dropdown',
    'hiddenId' => 'autocomplete_hidden',
    'itemLabel' => 'name',
    'itemKey' => 'id',
    'placeholder' => 'Rechercher...',
    'label' => '',
])

<div>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-semibold text-gray-700 mb-3">
            {{ $label }}
        </label>
    @endif
    <div class="relative">
        <input type="text" id="{{ $inputId }}" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50/50 focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200 mb-1"
            placeholder="{{ $placeholder }}" autocomplete="off">
        <input type="hidden" id="{{ $hiddenId }}" name="{{ $hiddenId }}">
        <ul id="{{ $dropdownId }}" class="absolute left-0 right-0 z-10 bg-white rounded-xl shadow-lg border border-gray-200 mt-1 max-h-48 overflow-y-auto hidden">
            @foreach($items as $item)
                <li class="autocomplete-item px-4 py-2 cursor-pointer hover:bg-blue-100 rounded flex items-center gap-2 hidden"
                    data-label="{{ strtolower($item[$itemLabel]) }}" data-id="{{ $item[$itemKey] }}">
                    {{ $item[$itemLabel] }}
                </li>
            @endforeach
        </ul>
    </div>
</div>

@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[id$="_search"]').forEach(input => {
            const baseId = input.id.replace('_search', '');
            const dropdown = document.getElementById(baseId + '_dropdown');
            const hiddenId = document.getElementById('user_id');
            const items = dropdown.querySelectorAll('.autocomplete-item');

            input.addEventListener('input', function() {
                const value = this.value.trim().toLowerCase();
                let anyVisible = false;
                items.forEach(item => {
                    if (item.dataset.label.includes(value) && value.length > 0) {
                        item.classList.remove('hidden');
                        anyVisible = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden', !anyVisible);
                hiddenId.value = '';
            });

            items.forEach(item => {
                item.addEventListener('click', function() {
                    input.value = item.dataset.label;
                    hiddenId.value = item.dataset.id;
                    dropdown.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    });
    </script>
    @endpush
@endonce