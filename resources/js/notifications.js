/**
 * Alpine notification dropdown — lazy init on first bell click.
 */
import Alpine from 'alpinejs';
import { ensureEcho } from './echo';

export function registerNotificationDropdown() {
    let initialNotifications = null;
    let initialFetchPromise = null;

    const csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const notificationsUrl = () =>
        document.querySelector('meta[name="notifications-index-url"]')?.content ?? '/api/notifications';

    Alpine.data('notificationDropdown', () => ({
        open: false,
        notifications: [],
        unreadCount: 0,
        ready: false,
        userId: null,
        echoSubscribed: false,

        init() {
            this.userId = this.$el.dataset.userId || null;
            this.prefetchUnreadCount();
        },

        prefetchUnreadCount() {
            if (initialNotifications) {
                this.notifications = [...initialNotifications];
                this.unreadCount = this.notifications.filter((n) => !n.read_at).length;
                return;
            }

            if (!initialFetchPromise) {
                initialFetchPromise = fetch(notificationsUrl())
                    .then((res) => (res.ok ? res.json() : []))
                    .then((data) => {
                        initialNotifications = Array.isArray(data) ? data : [];
                        return initialNotifications;
                    })
                    .catch(() => [])
                    .finally(() => {
                        initialFetchPromise = null;
                    });
            }

            initialFetchPromise.then((data) => {
                this.notifications = [...data];
                this.unreadCount = this.notifications.filter((n) => !n.read_at).length;
            });
        },

        async handleBellClick() {
            if (!this.open && !this.ready) {
                await this.bootstrap();
            }
            this.open = !this.open;
        },

        async bootstrap() {
            if (this.ready || !this.userId) {
                return;
            }

            this.ready = true;
            await this.fetchNotifications(true);
            this.subscribeEcho();
        },

        async fetchNotifications(updateCache = false) {
            try {
                const res = await fetch(notificationsUrl());
                if (!res.ok) {
                    return;
                }
                const data = await res.json();
                this.notifications = data;
                this.unreadCount = this.notifications.filter((n) => !n.read_at).length;
                if (updateCache) {
                    initialNotifications = Array.isArray(data) ? data : [];
                }
            } catch (e) {
                console.error('Failed to fetch notifications', e);
            }
        },

        subscribeEcho() {
            if (this.echoSubscribed || !this.userId) {
                return;
            }

            const echo = ensureEcho();
            if (!echo) {
                return;
            }

            this.echoSubscribed = true;
            const self = this;

            echo.private(`App.Models.User.${this.userId}`).notification((notification) => {
                if (!notification) {
                    return;
                }

                const exists = self.notifications.some((n) => n.id === notification.id);
                if (exists) {
                    return;
                }

                self.notifications.unshift({
                    id: notification.id,
                    data: notification,
                    created_at: new Date().toISOString(),
                    read_at: null,
                });
                self.unreadCount++;

                if (window.__lastNotifId === notification.id) {
                    return;
                }
                window.__lastNotifId = notification.id;

                let body = notification.message;
                if (notification.kolektor) {
                    body += '\nKolektor: ' + notification.kolektor;
                }

                if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                    new Notification(notification.title, { body });
                }
            });
        },

        requestNotificationPermission() {
            if (typeof Notification === 'undefined') {
                return;
            }
            if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                Notification.requestPermission();
            }
        },

        async markAsRead(id) {
            await fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            });
            const item = this.notifications.find((n) => n.id === id);
            if (item && !item.read_at) {
                item.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        },

        async markAllAsRead() {
            await fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            });
            this.notifications.forEach((n) => {
                n.read_at = new Date().toISOString();
            });
            this.unreadCount = 0;
        },

        async deleteNotification(id) {
            await fetch(`/api/notifications/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            });
            this.notifications = this.notifications.filter((n) => n.id !== id);
            this.unreadCount = this.notifications.filter((n) => !n.read_at).length;
        },

        async deleteAllNotifications() {
            await fetch('/api/notifications', {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken() },
            });
            this.notifications = [];
            this.unreadCount = 0;
        },
    }));
}
