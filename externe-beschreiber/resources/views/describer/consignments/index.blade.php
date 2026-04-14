@extends('layouts.app')
@section('title', __('messages.my_consignments'))
@section('content')
<h2 class="text-2xl font-bold mb-4">{{ __('messages.my_consignments') }}</h2>
<div class="grid gap-4">
    @forelse($consignments as $consignment)
        <div class="relative bg-white rounded-lg shadow hover:shadow-md transition">
            <a href="{{ route('describer.consignments.show', $consignment) }}"
               class="block p-4">
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
                    <div class="flex items-center gap-3">
                        <span class="text-indigo-500" title="{{ __('messages.show') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </span>
                    </div>
                </div>
            </a>
            <span class="absolute top-4 right-4 {{ $consignment->isOpen() ? 'text-green-600' : 'text-red-600' }}" title="{{ $consignment->isOpen() ? __('messages.open') : __('messages.closed') }}">
                @if($consignment->isOpen())
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                @else
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                @endif
            </span>
        </div>
    @empty
        <p class="text-gray-500">Keine Einlieferungen vorhanden.</p>
    @endforelse
</div>
@endsection
