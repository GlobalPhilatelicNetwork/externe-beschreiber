@extends('layouts.app')
@section('title', __('auth.login'))
@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">{{ __('auth.login') }}</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth.email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror" required autofocus>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth.password') }}</label>
                <input type="password" name="password" id="password"
                       class="w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror" required>
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">{{ __('auth.remember_me') }}</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white rounded py-2 px-4 hover:bg-indigo-700">{{ __('auth.login') }}</button>
        </form>
    </div>
</div>
@endsection
