<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">
        @if($editMode)
            {{ __('messages.edit') }} — {{ __('messages.sequence_number') }}
            {{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}
        @else
            {{ __('messages.new_lot') }} — {{ __('messages.sequence_number') }}
            {{ str_pad($consignment->next_number, 3, '0', STR_PAD_LEFT) }}
        @endif
    </div>

    <form method="POST"
          action="{{ $editMode ? route('describer.lots.update', [$consignment, $lot]) : route('describer.lots.store', $consignment) }}"
          id="lot-form"
          onsubmit="syncEditors()">
        @csrf
        @if($editMode)
            @method('PUT')
        @endif

        {{-- Hidden inputs for array values --}}
        @foreach($selectedCategoryIds as $cid)
            <input type="hidden" name="category_ids[]" value="{{ $cid }}">
        @endforeach
        @foreach($selectedConditionIds as $cid)
            <input type="hidden" name="condition_ids[]" value="{{ $cid }}">
        @endforeach
        @foreach($selectedDestinationIds as $did)
            <input type="hidden" name="destination_ids[]" value="{{ $did }}">
        @endforeach
        <input type="hidden" name="grouping_category_id" value="{{ $selectedGroupingCategoryId }}">
        <input type="hidden" name="lot_type" value="{{ $lot_type }}">

        {{-- Zeile 1: Kategorien (2fr) + Losart (1fr) --}}
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div class="col-span-2 relative">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.categories') }} <span class="text-red-500">*</span></label>
                {{-- Selected chips --}}
                <div class="flex flex-wrap gap-1 mb-1">
                    @foreach($this->selectedCategories as $cat)
                        <span class="inline-flex items-center bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">
                            {{ $cat->name }}
                            <button type="button" wire:click="removeCategory({{ $cat->id }})" class="ml-1 text-indigo-600 hover:text-indigo-900">&times;</button>
                        </span>
                    @endforeach
                </div>
                <input type="text"
                       wire:model.live.debounce.200ms="categorySearch"
                       wire:focus="$set('showCategoryDropdown', true)"
                       placeholder="{{ __('messages.filter_placeholder') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                       autocomplete="off">
                @if($showCategoryDropdown && $this->filteredCategories->count())
                    <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-b shadow-lg mt-0 max-h-48 overflow-y-auto">
                        @foreach($this->filteredCategories as $cat)
                            <div wire:click="selectCategory({{ $cat->id }})"
                                 class="px-3 py-2 text-sm hover:bg-indigo-100 cursor-pointer">{{ $cat->name }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.lot_type') }}</label>
                <div class="flex items-center gap-4 pt-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" wire:model="lot_type" value="single" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-1 text-sm">{{ __('messages.single_lot') }}</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" wire:model="lot_type" value="collection" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-1 text-sm">{{ __('messages.collection') }}</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Zeile 2: Gruppe (2fr) + Startpreis (1fr) --}}
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div class="col-span-2 relative">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.grouping_category') }}</label>
                <div class="flex items-center gap-1">
                    <input type="text"
                           wire:model.live.debounce.200ms="groupingCategorySearch"
                           wire:focus="$set('showGroupingCategoryDropdown', true)"
                           placeholder="{{ __('messages.filter_placeholder') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           autocomplete="off">
                    @if($selectedGroupingCategoryId)
                        <button type="button" wire:click="clearGroupingCategory" class="text-gray-400 hover:text-gray-600 text-lg px-1">&times;</button>
                    @endif
                </div>
                @if($showGroupingCategoryDropdown && $this->filteredGroupingCategories->count())
                    <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-b shadow-lg mt-0 max-h-48 overflow-y-auto">
                        @foreach($this->filteredGroupingCategories as $gc)
                            <div wire:click="selectGroupingCategory({{ $gc->id }}, '{{ addslashes($gc->name) }}')"
                                 class="px-3 py-2 text-sm hover:bg-indigo-100 cursor-pointer">{{ $gc->name }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.starting_price') }} <span class="text-red-500">*</span></label>
                <input type="number" name="starting_price" wire:model="starting_price" step="0.01" min="0" placeholder="0,00"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                       {{ $is_bid_lot ? 'disabled' : '' }}>
                <input type="hidden" name="is_bid_lot" value="{{ $is_bid_lot ? '1' : '0' }}">
                <label class="inline-flex items-center gap-1.5 mt-1 cursor-pointer text-sm text-gray-600">
                    <input type="checkbox" wire:model.live="is_bid_lot" class="accent-indigo-600">
                    {{ __('messages.bid_lot') }}
                </label>
            </div>
        </div>

        {{-- Zeile 3: Katalogeinträge (gesamte Breite) --}}
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_entries') }}</label>
            @foreach($catalogEntries as $idx => $entry)
                <div class="flex items-center gap-1 mb-1">
                    <select name="catalog_entries[{{ $idx }}][catalog_type_id]"
                            wire:model="catalogEntries.{{ $idx }}.catalog_type_id"
                            class="w-48 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">{{ __('messages.catalog_type') }}...</option>
                        @foreach($catalogTypes as $ct)
                            <option value="{{ $ct->id }}">{{ $ct->name }}</option>
                        @endforeach
                    </select>
                    <input type="text"
                           name="catalog_entries[{{ $idx }}][catalog_number]"
                           wire:model="catalogEntries.{{ $idx }}.catalog_number"
                           placeholder="{{ __('messages.catalog_number') }}"
                           class="flex-1 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @if(count($catalogEntries) > 1)
                        <button type="button" wire:click="removeCatalogEntry({{ $idx }})"
                                class="text-red-400 hover:text-red-600 text-lg px-1">&times;</button>
                    @endif
                </div>
            @endforeach
            <button type="button" wire:click="addCatalogEntry"
                    class="text-xs text-indigo-600 hover:text-indigo-800 mt-1">+ {{ __('messages.add_catalog_entry') }}</button>
        </div>

        {{-- Zeile 4: Losbeschreibung (contenteditable HTML editor) --}}
        <div class="mb-3" wire:ignore>
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.description') }} <span class="text-red-500">*</span></label>
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="flex gap-1 bg-gray-100 px-2 py-1 border-b border-gray-300">
                    <button type="button" onclick="execCmd('description', 'bold')" class="px-2 py-0.5 text-sm font-bold hover:bg-gray-200 rounded" title="Bold">B</button>
                    <button type="button" onclick="execCmd('description', 'italic')" class="px-2 py-0.5 text-sm italic hover:bg-gray-200 rounded" title="Italic">I</button>
                    <button type="button" onclick="execCmd('description', 'underline')" class="px-2 py-0.5 text-sm underline hover:bg-gray-200 rounded" title="Underline">U</button>
                    <button type="button" onclick="execCmd('description', 'strikeThrough')" class="px-2 py-0.5 text-sm line-through hover:bg-gray-200 rounded" title="Strikethrough">S</button>
                </div>
                <div id="editor-description"
                     contenteditable="true"
                     class="px-3 py-2 text-sm focus:outline-none"
                     style="min-height: 9rem;"
                     data-placeholder="{{ __('messages.description') }}..."></div>
            </div>
            <textarea name="description" id="hidden-description" class="hidden"></textarea>
        </div>

        {{-- Zeile 5: Provenance (contenteditable HTML editor) --}}
        <div class="mb-3" wire:ignore>
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.provenance') }}</label>
            <div class="border border-gray-300 rounded overflow-hidden">
                <div class="flex gap-1 bg-gray-100 px-2 py-1 border-b border-gray-300">
                    <button type="button" onclick="execCmd('provenance', 'bold')" class="px-2 py-0.5 text-sm font-bold hover:bg-gray-200 rounded" title="Bold">B</button>
                    <button type="button" onclick="execCmd('provenance', 'italic')" class="px-2 py-0.5 text-sm italic hover:bg-gray-200 rounded" title="Italic">I</button>
                    <button type="button" onclick="execCmd('provenance', 'underline')" class="px-2 py-0.5 text-sm underline hover:bg-gray-200 rounded" title="Underline">U</button>
                    <button type="button" onclick="execCmd('provenance', 'strikeThrough')" class="px-2 py-0.5 text-sm line-through hover:bg-gray-200 rounded" title="Strikethrough">S</button>
                </div>
                <div id="editor-provenance"
                     contenteditable="true"
                     class="px-3 py-2 text-sm focus:outline-none"
                     style="min-height: 9rem;"
                     data-placeholder="{{ __('messages.provenance') }}..."></div>
            </div>
            <textarea name="provenance" id="hidden-provenance" class="hidden"></textarea>
        </div>

        {{-- Zeile 6: Erhaltung (toggle buttons) --}}
        <div class="mb-3">
            <div class="flex items-center gap-4">
                <label class="text-sm text-gray-600 shrink-0">{{ __('messages.condition') }} <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-1">
                    @foreach($conditions as $condition)
                        <button type="button"
                                wire:click="toggleCondition({{ $condition->id }})"
                                class="px-3 py-1.5 text-xs rounded border transition-colors
                                    {{ in_array($condition->id, $selectedConditionIds)
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-indigo-400' }}">
                            @if($condition->image)
                                <img src="{{ $condition->image }}" alt="{{ $condition->name }}" class="w-5 h-5">
                            @else
                                {{ $condition->name }}
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Zeile 7: Destination (2fr) + EPos (1fr) --}}
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div class="col-span-2 relative">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.destination') }}</label>
                <div class="flex flex-wrap gap-1 mb-1">
                    @foreach($this->selectedDestinations as $dest)
                        <span class="inline-flex items-center bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                            {{ $dest->name }}
                            <button type="button" wire:click="removeDestination({{ $dest->id }})" class="ml-1 text-purple-600 hover:text-purple-900">&times;</button>
                        </span>
                    @endforeach
                </div>
                <input type="text"
                       wire:model.live.debounce.200ms="destinationSearch"
                       wire:focus="$set('showDestinationDropdown', true)"
                       placeholder="{{ __('messages.filter_placeholder') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                       autocomplete="off">
                @if($showDestinationDropdown && $this->filteredDestinations->count())
                    <div class="absolute z-10 w-full bg-white border border-gray-300 rounded-b shadow-lg mt-0 max-h-48 overflow-y-auto">
                        @foreach($this->filteredDestinations as $dest)
                            <div wire:click="selectDestination({{ $dest->id }})"
                                 class="px-3 py-2 text-sm hover:bg-indigo-100 cursor-pointer">{{ $dest->name }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.epos') }}</label>
                <input type="text" name="epos" wire:model="epos" placeholder="{{ __('messages.epos') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>

        {{-- Zeile 8: Verpackung (gesamte Breite) --}}
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.packaging') }}</label>
            @foreach($packageEntries as $idx => $entry)
                <div class="flex items-center gap-1 mb-1">
                    <select name="package_entries[{{ $idx }}][pack_type_id]"
                            wire:model="packageEntries.{{ $idx }}.pack_type_id"
                            class="w-28 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">{{ __('messages.pack_type') }}...</option>
                        @foreach($packTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                    <input type="text"
                           name="package_entries[{{ $idx }}][number]"
                           wire:model="packageEntries.{{ $idx }}.number"
                           placeholder="Nr."
                           class="w-16 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <input type="text"
                           name="package_entries[{{ $idx }}][notes]"
                           wire:model="packageEntries.{{ $idx }}.notes"
                           placeholder="{{ __('messages.notes') }}"
                           class="flex-1 border border-gray-300 rounded px-2 py-1.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <button type="button" wire:click="removePackageEntry({{ $idx }})"
                            class="text-red-400 hover:text-red-600 text-lg px-1">&times;</button>
                </div>
            @endforeach
            <button type="button" wire:click="addPackageEntry"
                    class="text-xs text-indigo-600 hover:text-indigo-800 mt-1">+ {{ __('messages.add_package') }}</button>
        </div>

        {{-- Zeile 8: Bemerkung --}}
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.notes') }}</label>
            <input type="text" name="notes" wire:model="notes"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                   placeholder="{{ __('messages.notes') }} (optional)">
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-2">
            <button type="button"
                    onclick="document.getElementById('lot-form').classList.add('hidden')"
                    class="px-4 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-100 text-sm">
                {{ __('messages.cancel') }}
            </button>
            <button type="submit"
                    class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800 text-sm">
                {{ $editMode ? __('messages.save') : __('messages.save_and_next') }}
            </button>
        </div>
    </form>

    {{-- Editor JS --}}
    <script>
        function execCmd(editorName, command) {
            document.getElementById('editor-' + editorName).focus();
            document.execCommand(command, false, null);
        }

        function syncEditors() {
            document.getElementById('hidden-description').value =
                document.getElementById('editor-description').innerHTML;
            document.getElementById('hidden-provenance').value =
                document.getElementById('editor-provenance').innerHTML;
        }

        @if($editMode)
            document.addEventListener('DOMContentLoaded', function() {
                var descEl = document.getElementById('editor-description');
                var provEl = document.getElementById('editor-provenance');
                if (descEl) descEl.innerHTML = @json($lot->description ?? '');
                if (provEl) provEl.innerHTML = @json($lot->provenance ?? '');
            });
        @endif
    </script>
</div>
