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
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.sequence_number') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.category') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.description') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.catalog_type') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.catalog_number') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.starting_price') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->lots->sortBy('sequence_number') as $lot)
                <tr class="border-t">
                    <td class="px-4 py-2 font-mono text-amber-600">{{ str_pad($lot->sequence_number, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-4 py-2">{{ $lot->category->name }}</td>
                    <td class="px-4 py-2 max-w-xs truncate">{{ $lot->description }}</td>
                    <td class="px-4 py-2">{{ $lot->catalogType->name }}</td>
                    <td class="px-4 py-2">{{ $lot->catalog_number }}</td>
                    <td class="px-4 py-2">{{ number_format($lot->starting_price, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
