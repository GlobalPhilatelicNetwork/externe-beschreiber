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
            <button onclick="document.getElementById('lot-form').classList.toggle('hidden')"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
                + {{ __('messages.new_lot') }}
            </button>
        @endif
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.sequence_number') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.lot_type') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.categories') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.description') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.condition') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.starting_price') }}</th>
                @if($consignment->isOpen())
                    <th class="text-left px-4 py-2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($consignment->lots->sortBy('sequence_number') as $lot)
                <tr class="border-t">
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
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('describer.lots.edit', [$consignment, $lot]) }}"
                               class="text-indigo-600 hover:underline text-xs">{{ __('messages.edit') }}</a>
                            <form method="POST" action="{{ route('describer.lots.destroy', [$consignment, $lot]) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if($consignment->isOpen())
    <div id="lot-form" class="mt-4 hidden">
        @livewire('lot-form', ['consignment' => $consignment])
    </div>
@endif
@endsection
