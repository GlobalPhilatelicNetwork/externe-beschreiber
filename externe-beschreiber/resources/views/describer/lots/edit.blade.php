@extends('layouts.app')
@section('title', __('messages.edit') . ' - ' . __('messages.lots'))
@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('describer.consignments.show', $consignment) }}" class="text-indigo-600 hover:underline">← {{ __('messages.my_consignments') }}</a>
    <h2 class="text-2xl font-bold mt-2 mb-4">{{ __('messages.edit') }} — {{ __('messages.sequence_number') }} {{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}</h2>
    <form method="POST" action="{{ route('describer.lots.update', [$consignment, $lot]) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.category') }}</label>
            <select name="category_id" class="w-full border rounded px-3 py-2">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $lot->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_type') }}</label>
                <select name="catalog_type_id" class="w-full border rounded px-3 py-2">
                    @foreach($catalogTypes as $ct)
                        <option value="{{ $ct->id }}" {{ $lot->catalog_type_id == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_number') }}</label>
                <input type="text" name="catalog_number" value="{{ old('catalog_number', $lot->catalog_number) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.starting_price') }}</label>
                <input type="number" name="starting_price" value="{{ old('starting_price', $lot->starting_price) }}" step="0.01" min="0" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.description') }}</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2 resize-y">{{ old('description', $lot->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="block text-sm text-gray-600 mb-1">{{ __('messages.notes') }}</label>
            <input type="text" name="notes" value="{{ old('notes', $lot->notes) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('describer.consignments.show', $consignment) }}" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">{{ __('messages.cancel') }}</a>
            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">{{ __('messages.save') }}</button>
        </div>
    </form>
</div>
@endsection
