@extends('layouts.app')
@section('title', __('messages.consignor_number') . ': ' . $consignment->consignor_number)
@section('content')
<div class="flex justify-between items-center mb-4">
    <div>
        <a href="{{ route('describer.consignments.index') }}" class="text-indigo-600 hover:underline">← {{ __('messages.my_consignments') }}</a>
        <h2 class="text-2xl font-bold mt-1">{{ __('messages.consignor_number') }}: {{ $consignment->consignor_number }}</h2>
        <p class="text-gray-500">NID: {{ $consignment->internal_nid }} | {{ __('messages.catalog_part') }}: {{ $consignment->catalogPart->name }}</p>
    </div>
    <span class="px-3 py-1 rounded-full text-sm {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
    </span>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
        <h3 class="font-bold">{{ __('messages.lots') }} ({{ $consignment->lots->count() }})</h3>
        @if($consignment->isOpen())
            <button onclick="document.getElementById('lot-form-wrapper').classList.toggle('hidden')"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
                + {{ __('messages.new_lot') }}
            </button>
        @endif
    </div>
    <table class="w-full text-sm" id="lots-table">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="sequence_number">
                    {{ __('messages.sequence_number') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="lot_type">
                    {{ __('messages.lot_type') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="categories">
                    {{ __('messages.categories') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="description">
                    {{ __('messages.description') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="condition">
                    {{ __('messages.condition') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                <th class="text-left px-4 py-2 cursor-pointer select-none hover:bg-gray-100" data-sort="starting_price">
                    {{ __('messages.starting_price') }}
                    <span class="sort-icon text-gray-400 text-xs ml-1">↕</span>
                </th>
                @if($consignment->isOpen())
                    <th class="text-left px-4 py-2 w-20"></th>
                @endif
            </tr>
            <tr class="bg-gray-50 border-t">
                <th class="px-4 py-1"></th>
                <th class="px-4 py-1"></th>
                <th class="px-4 py-1">
                    <input type="text" data-filter="categories" placeholder="{{ __('messages.filter_placeholder') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1 text-xs font-normal focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </th>
                <th class="px-4 py-1">
                    <input type="text" data-filter="description" placeholder="{{ __('messages.filter_placeholder') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1 text-xs font-normal focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </th>
                <th class="px-4 py-1"></th>
                <th class="px-4 py-1">
                    <input type="text" data-filter="starting_price" placeholder="{{ __('messages.filter_placeholder') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1 text-xs font-normal focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </th>
                @if($consignment->isOpen())
                    <th class="px-4 py-1"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->lots->sortBy('sequence_number') as $lot)
                <tr class="border-t lot-row" data-categories="{{ $lot->categories->pluck('name')->join(', ') }}" data-description="{{ strip_tags($lot->description) }}" data-starting_price="{{ number_format($lot->starting_price, 2, ',', '.') }}">
                    <td class="px-4 py-2 font-mono text-amber-600">{{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-2">
                        @if($lot->lot_type === 'single')
                            <span class="inline-block px-1.5 py-0.5 rounded text-xs bg-blue-100 text-blue-800 font-semibold">E</span>
                        @elseif($lot->lot_type === 'collection')
                            <span class="inline-block px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-800 font-semibold">S</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $lot->categories->pluck('name')->join(', ') }}</td>
                    <td class="px-4 py-2 max-w-xs truncate">{{ \Illuminate\Support\Str::limit(strip_tags($lot->description), 80) }}</td>
                    <td class="px-4 py-2">{{ $lot->conditions->pluck('name')->join(', ') }}</td>
                    <td class="px-4 py-2">{{ number_format($lot->starting_price, 2, ',', '.') }} €</td>
                    @if($consignment->isOpen())
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('describer.lots.edit', [$consignment, $lot]) }}"
                                   class="text-indigo-500 hover:text-indigo-700" title="{{ __('messages.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('describer.lots.destroy', [$consignment, $lot]) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="{{ __('messages.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($consignment->isOpen())
    <div id="lot-form-wrapper" class="mt-4 {{ $errors->any() ? '' : 'hidden' }}">
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @livewire('lot-form', ['consignment' => $consignment])
    </div>
@endif

<script>
(function() {
    const table = document.getElementById('lots-table');
    const tbody = table.querySelector('tbody');
    let sortCol = null;
    let sortDir = 'asc';

    // Sortierung
    table.querySelectorAll('th[data-sort]').forEach(function(th) {
        th.addEventListener('click', function() {
            const col = th.dataset.sort;
            if (sortCol === col) {
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortCol = col;
                sortDir = 'asc';
            }
            table.querySelectorAll('.sort-icon').forEach(function(icon) { icon.textContent = '↕'; });
            th.querySelector('.sort-icon').textContent = sortDir === 'asc' ? '↑' : '↓';

            var rows = Array.from(tbody.querySelectorAll('tr.lot-row'));
            rows.sort(function(a, b) {
                var aVal, bVal;
                var cells_a = a.querySelectorAll('td');
                var cells_b = b.querySelectorAll('td');
                var colMap = {sequence_number: 0, lot_type: 1, categories: 2, description: 3, condition: 4, starting_price: 5};
                var idx = colMap[col];
                aVal = cells_a[idx].textContent.trim();
                bVal = cells_b[idx].textContent.trim();

                if (col === 'sequence_number' || col === 'starting_price') {
                    aVal = parseFloat(aVal.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
                    bVal = parseFloat(bVal.replace(/[^\d,.-]/g, '').replace(',', '.')) || 0;
                    return sortDir === 'asc' ? aVal - bVal : bVal - aVal;
                }
                return sortDir === 'asc' ? aVal.localeCompare(bVal, 'de') : bVal.localeCompare(aVal, 'de');
            });
            rows.forEach(function(row) { tbody.appendChild(row); });
        });
    });

    // Filter as you type
    table.querySelectorAll('input[data-filter]').forEach(function(input) {
        input.addEventListener('input', function() { applyFilters(); });
    });

    function applyFilters() {
        var filters = {};
        table.querySelectorAll('input[data-filter]').forEach(function(input) {
            var val = input.value.toLowerCase().trim();
            if (val) filters[input.dataset.filter] = val;
        });

        tbody.querySelectorAll('tr.lot-row').forEach(function(row) {
            var visible = true;
            for (var key in filters) {
                var data = (row.dataset[key] || '').toLowerCase();
                if (data.indexOf(filters[key]) === -1) {
                    visible = false;
                    break;
                }
            }
            row.style.display = visible ? '' : 'none';
        });
    }
})();
</script>
@endsection
