<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Protika WiFi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased selection:bg-primary/20 selection:text-primary-deep" x-data="{ userMenuOpen: false, masterMenuOpen: false, tagihanMenuOpen: false, mobileMenuOpen: false }">

    {{-- Main Navigation Bar --}}
    @include('layouts.components.navbar')

    {{-- Main Content --}}
    <main class="max-w-[1440px] mx-auto px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-80px)] pb-24 md:pb-8">
        {{-- Header Section --}}
        <div class="mb-8 md:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-6 animate-fade-in-up">
            <div class="space-y-1">
                <h1 class="text-3xl md:text-4xl font-extrabold text-content-primary tracking-tight">@yield('page-title', 'Dashboard')</h1>
                <p class="text-content-secondary md:text-lg font-medium">@yield('page-subtitle', 'Welcome back, ' . auth()->user()->name)</p>
            </div>
            
            <div class="flex items-center gap-3">
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
