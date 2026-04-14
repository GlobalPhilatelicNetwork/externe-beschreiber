@extends('layouts.app')
@section('title', __('messages.edit') . ' - ' . __('messages.lots'))
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('describer.consignments.show', $consignment) }}" class="text-indigo-600 hover:underline">← {{ __('messages.my_consignments') }}</a>

    @if($errors->any())
        <div class="mt-2 mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-4">
        @livewire('lot-form', ['consignment' => $consignment, 'lot' => $lot])
    </div>
</div>
@endsection
