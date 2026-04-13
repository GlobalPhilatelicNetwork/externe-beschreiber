@extends('layouts.app')
@section('title', __('messages.my_consignments'))
@section('content')
<h2 class="text-2xl font-bold mb-4">{{ __('messages.my_consignments') }}</h2>
<div class="grid gap-4">
    @forelse($consignments as $consignment)
        <a href="{{ route('describer.consignments.show', $consignment) }}"
           class="block bg-white rounded-lg shadow p-4 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-bold text-lg">{{ __('messages.consignor_number') }}: {{ $consignment->consignor_number }}</div>
                    <div class="text-gray-500 text-sm">
                        NID: {{ $consignment->internal_nid }}
                        | {{ __('messages.start_number') }}: {{ str_pad($consignment->start_number, 3, '0', STR_PAD_LEFT) }}
                        | {{ $consignment->lots->count() }} {{ __('messages.lots') }}
                    </div>
                    <div class="text-gray-500 text-sm">{{ __('messages.catalog_part') }}: {{ $consignment->catalogPart->name }}</div>
                </div>
                <span class="px-3 py-1 rounded-full text-sm {{ $consignment->isOpen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}
                </span>
            </div>
        </a>
    @empty
        <p class="text-gray-500">Keine Einlieferungen vorhanden.</p>
    @endforelse
</div>
@endsection
