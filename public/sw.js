// Service Worker for Push Notifications
self.addEventListener('push', function(event) {
    if (event.data) {
        const data = event.data.json();
        const options = {
            body: data.body || data.message || 'New notification',
            icon: '/img/icon-192x192.png',
            badge: '/img/badge-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                url: data.url || '/dashboard',
                id: data.id
            },
            actions: [
                { action: 'open', title: 'Buka', icon: '/img/icon-192x192.png' }
            ]
        };
        event.waitUntil(
            self.registration.showNotification(data.title || 'Presensi Digital', options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                const url = event.notification.data?.url || '/dashboard';
                for (const client of clientList) {
                    if (client.url.includes(self.registration.scope) && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});
