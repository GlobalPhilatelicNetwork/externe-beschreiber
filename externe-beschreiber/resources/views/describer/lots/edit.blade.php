@extends('layouts.app')
@section('title', __('messages.edit') . ' - ' . __('messages.lots'))
@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route('describer.consignments.show', $consignment) }}" class="text-indigo-600 hover:underline">← {{ __('messages.my_consignments') }}</a>
    <h2 class="text-2xl font-bold mt-2 mb-4">{{ __('messages.edit') }} — {{ __('messages.sequence_number') }} {{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('describer.lots.update', [$consignment, $lot]) }}" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Losart --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.lot_type') }}</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="lot_type" value="single" {{ old('lot_type', $lot->lot_type) === 'single' ? 'checked' : '' }} class="accent-indigo-600">
                    <span>{{ __('messages.single_lot') }}</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="lot_type" value="collection" {{ old('lot_type', $lot->lot_type) === 'collection' ? 'checked' : '' }} class="accent-indigo-600">
                    <span>{{ __('messages.collection') }}</span>
                </label>
            </div>
        </div>

        {{-- Kategorien (multi-select) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.categories') }}</label>
            <select name="category_ids[]" multiple class="w-full border rounded px-3 py-2 h-28">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ in_array($cat->id, old('category_ids', $lot->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.multiple_possible') }}</p>
        </div>

        {{-- Erhaltung (checkboxes) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.condition') }}</label>
            <div class="flex flex-wrap gap-3">
                @foreach($conditions as $cond)
                    <label class="flex items-center gap-1.5 cursor-pointer text-sm">
                        <input type="checkbox" name="condition_ids[]" value="{{ $cond->id }}"
                            {{ in_array($cond->id, old('condition_ids', $lot->conditions->pluck('id')->toArray())) ? 'checked' : '' }}
                            class="accent-indigo-600">
                        {{ $cond->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Destination (multi-select) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.destination') }}</label>
            <select name="destination_ids[]" multiple class="w-full border rounded px-3 py-2 h-24">
                @foreach($destinations as $dest)
                    <option value="{{ $dest->id }}" {{ in_array($dest->id, old('destination_ids', $lot->destinations->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $dest->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ __('messages.multiple_possible') }}</p>
        </div>

        {{-- Gruppe --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.grouping_category') }}</label>
            <select name="grouping_category_id" class="w-full border rounded px-3 py-2">
                <option value="">—</option>
                @foreach($groupingCategories as $gc)
                    <option value="{{ $gc->id }}" {{ old('grouping_category_id', $lot->grouping_category_id) == $gc->id ? 'selected' : '' }}>
                        {{ $gc->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Losbeschreibung --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.description') }}</label>
            <textarea name="description" rows="5" class="w-full border rounded px-3 py-2 resize-y">{{ old('description', $lot->description) }}</textarea>
        </div>

        {{-- Provenance --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.provenance') }}</label>
            <textarea name="provenance" rows="2" class="w-full border rounded px-3 py-2 resize-y">{{ old('provenance', $lot->provenance) }}</textarea>
        </div>

        {{-- EPos und Startpreis --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.epos') }}</label>
                <input type="text" name="epos" value="{{ old('epos', $lot->epos) }}" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">{{ __('messages.epos_hint') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.starting_price') }}</label>
                <input type="number" name="starting_price" value="{{ old('starting_price', $lot->starting_price) }}" step="0.01" min="0" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        {{-- Bemerkung --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.notes') }}</label>
            <input type="text" name="notes" value="{{ old('notes', $lot->notes) }}" class="w-full border rounded px-3 py-2">
        </div>

        {{-- Katalogeinträge --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.catalog_entries') }}</label>
            <div id="catalog-entries-container" class="space-y-2">
                @php $catalogEntries = old('catalog_entries', $lot->catalogEntries->map(fn($e) => ['catalog_type_id' => $e->catalog_type_id, 'catalog_number' => $e->catalog_number])->toArray()); @endphp
                @foreach($catalogEntries as $i => $entry)
                    <div class="flex gap-2 items-center catalog-entry-row">
                        <select name="catalog_entries[{{ $i }}][catalog_type_id]" class="border rounded px-2 py-1.5 text-sm">
                            <option value="">— {{ __('messages.catalog_type') }} —</option>
                            @foreach($catalogTypes as $ct)
                                <option value="{{ $ct->id }}" {{ ($entry['catalog_type_id'] ?? '') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="catalog_entries[{{ $i }}][catalog_number]" value="{{ $entry['catalog_number'] ?? '' }}" placeholder="{{ __('messages.catalog_number') }}" class="border rounded px-2 py-1.5 text-sm flex-1">
                        <button type="button" onclick="this.closest('.catalog-entry-row').remove()" class="text-red-500 hover:text-red-700 text-xs px-2">✕</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-catalog-entry" class="mt-2 text-sm text-indigo-600 hover:underline">{{ __('messages.add_catalog_entry') }}</button>
        </div>

        {{-- Verpackung --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.packaging') }}</label>
            <div id="packages-container" class="space-y-2">
                @php $packages = old('packages', $lot->packages->map(fn($p) => ['pack_type_id' => $p->pack_type_id, 'pack_number' => $p->pack_number, 'pack_note' => $p->pack_note])->toArray()); @endphp
                @foreach($packages as $i => $pkg)
                    <div class="flex gap-2 items-center package-row">
                        <select name="packages[{{ $i }}][pack_type_id]" class="border rounded px-2 py-1.5 text-sm">
                            <option value="">— {{ __('messages.pack_type') }} —</option>
                            @foreach($packTypes as $pt)
                                <option value="{{ $pt->id }}" {{ ($pkg['pack_type_id'] ?? '') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="packages[{{ $i }}][pack_number]" value="{{ $pkg['pack_number'] ?? '' }}" placeholder="{{ __('messages.pack_number') }}" class="border rounded px-2 py-1.5 text-sm w-20">
                        <input type="text" name="packages[{{ $i }}][pack_note]" value="{{ $pkg['pack_note'] ?? '' }}" placeholder="{{ __('messages.pack_note') }}" class="border rounded px-2 py-1.5 text-sm flex-1">
                        <button type="button" onclick="this.closest('.package-row').remove()" class="text-red-500 hover:text-red-700 text-xs px-2">✕</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-package" class="mt-2 text-sm text-indigo-600 hover:underline">{{ __('messages.add_package') }}</button>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('describer.consignments.show', $consignment) }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">{{ __('messages.cancel') }}</a>
            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">{{ __('messages.save') }}</button>
        </div>
    </form>
</div>

<script>
(function () {
    function nextIndex(container) {
        return container.querySelectorAll('[data-row]').length;
    }

    document.getElementById('add-catalog-entry').addEventListener('click', function () {
        const container = document.getElementById('catalog-entries-container');
        const idx = container.querySelectorAll('.catalog-entry-row').length;
        const selectOptions = `{!! collect($catalogTypes)->map(fn($ct) => '<option value="' . $ct->id . '">' . $ct->name . '</option>')->join('') !!}`;
        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center catalog-entry-row';
        row.innerHTML = `
            <select name="catalog_entries[${idx}][catalog_type_id]" class="border rounded px-2 py-1.5 text-sm">
                <option value="">— {{ __('messages.catalog_type') }} —</option>
                ${selectOptions}
            </select>
            <input type="text" name="catalog_entries[${idx}][catalog_number]" placeholder="{{ __('messages.catalog_number') }}" class="border rounded px-2 py-1.5 text-sm flex-1">
            <button type="button" onclick="this.closest('.catalog-entry-row').remove()" class="text-red-500 hover:text-red-700 text-xs px-2">✕</button>
        `;
        container.appendChild(row);
    });

    document.getElementById('add-package').addEventListener('click', function () {
        const container = document.getElementById('packages-container');
        const idx = container.querySelectorAll('.package-row').length;
        const selectOptions = `{!! collect($packTypes)->map(fn($pt) => '<option value="' . $pt->id . '">' . $pt->name . '</option>')->join('') !!}`;
        const row = document.createElement('div');
        row.className = 'flex gap-2 items-center package-row';
        row.innerHTML = `
            <select name="packages[${idx}][pack_type_id]" class="border rounded px-2 py-1.5 text-sm">
                <option value="">— {{ __('messages.pack_type') }} —</option>
                ${selectOptions}
            </select>
            <input type="text" name="packages[${idx}][pack_number]" placeholder="{{ __('messages.pack_number') }}" class="border rounded px-2 py-1.5 text-sm w-20">
            <input type="text" name="packages[${idx}][pack_note]" placeholder="{{ __('messages.pack_note') }}" class="border rounded px-2 py-1.5 text-sm flex-1">
            <button type="button" onclick="this.closest('.package-row').remove()" class="text-red-500 hover:text-red-700 text-xs px-2">✕</button>
        `;
        container.appendChild(row);
    });
})();
</script>
@endsection
