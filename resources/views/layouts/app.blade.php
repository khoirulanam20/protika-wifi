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
    <script>
        function notificationDropdown() {
            return {
                open: false,
                notifications: [],
                unreadCount: 0,
                
                async fetchNotifications() {
                    try {
                        const res = await fetch('{{ route('notifications.index') }}');
                        const data = await res.json();
                        this.notifications = data;
                        this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                    } catch (e) {
                        console.error('Failed to fetch notifications', e);
                    }
                },
                
                async markAsRead(id) {
                    await fetch(`/api/notifications/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                },
                
                async markAllAsRead() {
                    await fetch(`/api/notifications/read-all`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.notifications.forEach(n => n.read_at = new Date().toISOString());
                    this.unreadCount = 0;
                },
                
                async deleteNotification(id) {
                    await fetch(`/api/notifications/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.notifications = this.notifications.filter(n => n.id !== id);
                    this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                },
                
                async deleteAllNotifications() {
                    await fetch(`/api/notifications`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.notifications = [];
                    this.unreadCount = 0;
                },
                
                initNotifications(userId) {
                    this.fetchNotifications();
                    const self = this;
                    setTimeout(() => {
                        if (window.Echo) {
                            window.Echo.private(`App.Models.User.${userId}`)
                                .notification((notification) => {
                                    if (!notification) return;
                                    const exists = self.notifications.some(n => n.id === notification.id);
                                    if (exists) return;
                                    self.notifications.unshift({
                                        id: notification.id,
                                        data: notification,
                                        created_at: new Date().toISOString(),
                                        read_at: null
                                    });
                                    self.unreadCount++;
                                    
                                    // Only show ONE browser notification per unique notification
                                    if (window.__lastNotifId === notification.id) return;
                                    window.__lastNotifId = notification.id;
                                    
                                    let body = notification.message;
                                    if (notification.kolektor) {
                                        body += '\nKolektor: ' + notification.kolektor;
                                    }
                                    if(Notification.permission === 'granted') {
                                        new Notification(notification.title, { body });
                                    } else {
                                        alert(`${notification.title}\n${body}`);
                                    }
                                });
                        }
                    }, 1000);
                }
            }
        }
        
        // Request Notification permission
        document.addEventListener('DOMContentLoaded', () => {
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        });
    </script>
</body>
</html>
