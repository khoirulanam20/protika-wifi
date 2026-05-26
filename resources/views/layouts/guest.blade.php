<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Protika WiFi') }} — Login</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased selection:bg-primary/20 selection:text-primary-deep">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8"
             style="background: linear-gradient(135deg, #F0F4FF 0%, #EFF6FF 100%);">

            {{-- Branding --}}
            <div class="mb-8 animate-fade-in-up">
                <a href="/" class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full border border-border bg-white/70 backdrop-blur-sm shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white font-bold text-sm shadow-sm">
                        P
                    </div>
                    <span class="font-semibold text-content-primary text-lg">Protika WiFi</span>
                </a>
            </div>

            {{-- Login Card --}}
            <div class="w-full max-w-md animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="bg-white/70 backdrop-blur-xl border border-white/40 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8">
                    {{ $slot }}
                </div>

                <p class="text-center text-xs text-content-tertiary mt-6 animate-fade-in-up" style="animation-delay: 0.2s;">
                    &copy; {{ date('Y') }} Protika WiFi. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
