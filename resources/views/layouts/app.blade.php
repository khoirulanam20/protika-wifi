<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Protika WiFi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased selection:bg-primary/20 selection:text-primary-deep" x-data="{ userMenuOpen: false, masterMenuOpen: false, tagihanMenuOpen: false, mobileMenuOpen: false }">

    {{-- Main Navigation Bar --}}
    @include('layouts.components.navbar')

    {{-- Main Content --}}
    <main class="max-w-[1440px] mx-auto px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-80px)] pb-24 md:pb-8">
        {{-- Header Section --}}
        <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-semibold text-content-primary tracking-tight">@yield('page-title', 'Dashboard')</h1>
                <p class="text-content-secondary mt-1 text-sm md:text-base">@yield('page-subtitle', 'Welcome back, ' . auth()->user()->name)</p>
            </div>
            
            <div class="flex items-center gap-2">
                @yield('header-actions')
            </div>
        </div>

        {{-- Flash Messages --}}
        @include('layouts.components.flash-message')

        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation --}}
    @include('layouts.components.bottom-navbar')

    @stack('scripts')
</body>
</html>
