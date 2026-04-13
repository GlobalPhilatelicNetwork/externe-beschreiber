<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">
        {{ __('messages.new_lot') }} — {{ __('messages.sequence_number') }}
        {{ str_pad($consignment->next_number, 3, '0', STR_PAD_LEFT) }}
    </div>
    <form method="POST" action="{{ route('describer.lots.store', $consignment) }}">
        @csrf
        <div class="mb-3 relative">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.category') }}</label>
            <input type="text" wire:model.live.debounce.200ms="categorySearch"
                   wire:focus="$set('showCategoryDropdown', true)"
                   placeholder="{{ __('messages.filter_placeholder') }}"
                   class="w-full border rounded px-3 py-2" autocomplete="off">
            <input type="hidden" name="category_id" value="{{ $category_id }}">
            @if($showCategoryDropdown && $this->filteredCategories->count())
                <div class="absolute z-10 w-full bg-white border rounded-b shadow-lg mt-0">
                    @foreach($this->filteredCategories as $cat)
                        <div wire:click="selectCategory({{ $cat->id }}, '{{ $cat->name }}')"
                             class="px-3 py-2 hover:bg-indigo-100 cursor-pointer">{{ $cat->name }}</div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div class="relative">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_type') }}</label>
                <input type="text" wire:model.live.debounce.200ms="catalogTypeSearch"
                       wire:focus="$set('showCatalogTypeDropdown', true)"
                       placeholder="{{ __('messages.filter_placeholder') }}"
                       class="w-full border rounded px-3 py-2" autocomplete="off">
                <input type="hidden" name="catalog_type_id" value="{{ $catalog_type_id }}">
                @if($showCatalogTypeDropdown && $this->filteredCatalogTypes->count())
                    <div class="absolute z-10 w-full bg-white border rounded-b shadow-lg mt-0">
                        @foreach($this->filteredCatalogTypes as $ct)
                            <div wire:click="selectCatalogType({{ $ct->id }}, '{{ $ct->name }}')"
                                 class="px-3 py-2 hover:bg-indigo-100 cursor-pointer">{{ $ct->name }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_number') }}</label>
                <input type="text" name="catalog_number" wire:model="catalog_number" placeholder="z.B. 438" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.starting_price') }}</label>
                <input type="number" name="starting_price" wire:model="starting_price" step="0.01" min="0" placeholder="0,00" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.description') }}</label>
            <textarea name="description" wire:model="description" rows="4" class="w-full border rounded px-3 py-2 resize-y" placeholder="{{ __('messages.description') }}..."></textarea>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.notes') }}</label>
            <input type="text" name="notes" wire:model="notes" class="w-full border rounded px-3 py-2" placeholder="{{ __('messages.notes') }} (optional)">
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" onclick="document.getElementById('lot-form').classList.add('hidden')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">{{ __('messages.cancel') }}</button>
            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">{{ __('messages.save_and_next') }}</button>
        </div>
    </form>
</div>
