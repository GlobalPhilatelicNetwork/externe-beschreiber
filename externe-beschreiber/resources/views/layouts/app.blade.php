<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <span class="font-bold text-lg">Externe Beschreiber</span>
                @auth
                    @if(auth()->user()->isAdmin())
                        @if(Route::has('admin.users.index'))
                        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('messages.describers') }}</a>
                        @endif
                        @if(Route::has('admin.consignments.index'))
                        <a href="{{ route('admin.consignments.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('messages.consignments') }}</a>
                        @endif
                    @else
                        <a href="{{ route('describer.consignments.index') }}" class="text-gray-600 hover:text-gray-900">{{ __('messages.my_consignments') }}</a>
                    @endif
                @endauth
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('locale.switch', 'de') }}" class="{{ app()->getLocale() === 'de' ? 'font-bold' : '' }}">DE</a>
                    <span>/</span>
                    <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">EN</a>
                    <span class="text-gray-400">|</span>
                    <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">{{ __('auth.logout') }}</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 rounded px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 rounded px-4 py-3 mb-4">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
    @livewireScripts
</body>
</html>
