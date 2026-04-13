@extends('layouts.app')
@section('title', __('messages.describers'))
@section('content')
<div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-bold">{{ __('messages.describers') }} ({{ $users->count() }})</h2>
    <button onclick="document.getElementById('user-form').classList.toggle('hidden')"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm">
        + {{ __('messages.new_describer') }}
    </button>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-2">{{ __('messages.name') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.email') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.role') }}</th>
                <th class="text-left px-4 py-2">{{ __('messages.consignments') }}</th>
                <th class="px-4 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded-full text-xs {{ $user->isAdmin() ? 'bg-indigo-100 text-indigo-800' : 'bg-green-100 text-green-800' }}">
                            {{ $user->isAdmin() ? 'Admin' : __('messages.describers') }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $user->consignments_count }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        @if(!$user->isAdmin())
                            <form method="POST" action="{{ route('admin.users.send-credentials', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-amber-600">📧</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="user-form" class="hidden mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
    <div class="font-bold text-indigo-800 mb-3">{{ __('messages.new_describer') }}</div>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.name') }}</label>
                <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('auth.password') }}</label>
                <input type="text" name="password" class="w-full border rounded px-3 py-2" value="{{ \Illuminate\Support\Str::random(10) }}" required>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('messages.role') }}</label>
                <select name="role" class="w-full border rounded px-3 py-2">
                    <option value="user">{{ __('messages.describers') }}</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-3">
            <button type="button" onclick="document.getElementById('user-form').classList.add('hidden')" class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100">{{ __('messages.cancel') }}</button>
            <button type="submit" name="send_credentials" value="0" class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-800">{{ __('messages.save') }}</button>
            <button type="submit" name="send_credentials" value="1" class="px-4 py-2 bg-amber-700 text-white rounded hover:bg-amber-800">{{ __('messages.save_and_send') }} 📧</button>
        </div>
    </form>
</div>
@endsection
