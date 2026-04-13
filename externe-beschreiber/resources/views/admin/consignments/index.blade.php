@extends('layouts.app')
@section('title', __('messages.all_consignments'))
@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">{{ __('messages.all_consignments') }} ({{ $consignments->count() }})</h2>
    <button onclick="document.getElementById('consignment-form').classList.toggle('hidden')"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">+ {{ __('messages.new_consignment') }}</button>
</div>
<div class="flex gap-3 mb-4">
    <form method="GET" class="flex gap-3">
        <select name="status" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
            <option value="">{{ __('messages.all_status') }}</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('messages.open') }}</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
        </select>
        <select name="user_id" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
            <option value="">{{ __('messages.all_describers') }}</option>
            @foreach($describers as $describer)
                <option value="{{ $describer->id }}" {{ request('user_id') == $describer->id ? 'selected' : '' }}>{{ $describer->name }}</option>
            @endforeach
        </select>
    </form>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.consignor_number') }}</th>
                <th class="text-left px-4 py-2">NID</th>
                <th class="text-left px-4 py-2">{{ __('messages.sale_id') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.describers') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.lots') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.status') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignments as $consignment)
                <tr class="border-t">
                    <td class="px-4 py-2 font-bold">{{ $consignment->consignor_number }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $consignment->internal_nid }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $consignment->sale_id }}</td>
                    <td class="px-4 py-2">{{ $consignment->user->name }}</td>
                    <td class="px-4 py-2">{{ $consignment->lots_count }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
                        </span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('describer.consignments.show', $consignment) }}" class="text-indigo-600">&#128065;</a>
                        @if($consignment->isOpen())
                            <form method="POST" action="{{ route('admin.consignments.close', $consignment) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600">&#128274;</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="consignment-form" class="hidden mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">{{ __('messages.new_consignment') }}</div>
    <form method="POST" action="{{ route('admin.consignments.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.consignor_number') }}</label>
                <input type="text" name="consignor_number" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.internal_nid') }}</label>
                <input type="text" name="internal_nid" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.sale_id') }}</label>
                <input type="text" name="sale_id" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.start_number') }}</label>
                <input type="number" name="start_number" value="1" min="1" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.catalog_part') }}</label>
                <select name="catalog_part_id" class="w-full border rounded px-3 py-2">
                    @foreach($catalogParts as $part)
                        <option value="{{ $part->id }}" {{ $part->is_default ? 'selected' : '' }}>{{ $part->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.assign_to') }}</label>
                <select name="user_id" class="w-full border rounded px-3 py-2" required>
                    @foreach($describers as $describer)
                        <option value="{{ $describer->id }}">{{ $describer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button" onclick="document.getElementById('consignment-form').classList.add('hidden')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">{{ __('messages.cancel') }}</button>
            <button type="submit" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">{{ __('messages.create') }}</button>
        </div>
    </form>
</div>
@endsection
