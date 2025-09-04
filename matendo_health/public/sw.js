// MCares Service Worker - Enhanced PWA functionality
const CACHE_NAME = 'mcares-v1.2.0';
const OFFLINE_PAGE = '/offline.html';

// Resources to cache for offline functionality
const STATIC_CACHE_RESOURCES = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/images/logo.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/manifest.json',
    OFFLINE_PAGE
];

// API endpoints that should be cached
const API_CACHE_PATTERNS = [
    '/api/dashboard/metrics',
    '/api/patients/',
    '/api/vitals/',
    '/api/medications/',
    '/api/appointments/'
];

// Install event - cache static resources
self.addEventListener('install', (event) => {
    console.log('[ServiceWorker] Install');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[ServiceWorker] Caching static resources');
                return cache.addAll(STATIC_CACHE_RESOURCES);
            })
            .then(() => {
                // Force activation of new service worker
                return self.skipWaiting();
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[ServiceWorker] Activate');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[ServiceWorker] Removing old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            // Ensure the new SW takes control immediately
            return self.clients.claim();
        })
    );
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Handle different types of requests
    if (request.method === 'GET') {
        if (shouldCacheRequest(request)) {
            event.respondWith(handleCacheableRequest(request));
        } else if (isAPIRequest(request)) {
            event.respondWith(handleAPIRequest(request));
        } else if (isNavigationRequest(request)) {
            event.respondWith(handleNavigationRequest(request));
        }
    } else if (request.method === 'POST' && !navigator.onLine) {
        // Handle POST requests when offline
        event.respondWith(handleOfflinePost(request));
    }
});

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    console.log('[ServiceWorker] Background sync:', event.tag);
    
    if (event.tag === 'sync-offline-data') {
        event.waitUntil(syncOfflineData());
    } else if (event.tag === 'sync-vital-signs') {
        event.waitUntil(syncVitalSigns());
    }
});

// Push notifications
self.addEventListener('push', (event) => {
    console.log('[ServiceWorker] Push received:', event);
    
    const options = {
        body: 'You have new medical updates',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/images/icons/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/icons/xmark.png'
            }
        ]
    };

    if (event.data) {
        const data = event.data.json();
        options.body = data.message || options.body;
        options.data = { ...options.data, ...data };
        
        // Handle different notification types
        if (data.type === 'emergency') {
            options.requireInteraction = true;
            options.vibrate = [200, 100, 200, 100, 200];
            options.badge = '/images/icons/emergency.png';
        } else if (data.type === 'medication') {
            options.actions.unshift({
                action: 'mark-taken',
                title: 'Mark as Taken',
                icon: '/images/icons/pill.png'
            });
        }
    }

    event.waitUntil(
        self.registration.showNotification('MCares', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('[ServiceWorker] Notification click:', event);
    
    event.notification.close();

    const action = event.action;
    const data = event.notification.data;

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((clientList) => {
            // Try to focus existing window first
            for (let client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    return client.focus().then(() => {
                        return handleNotificationAction(client, action, data);
                    });
                }
            }
            
            // Open new window if none exists
            if (clients.openWindow) {
                const url = getNotificationUrl(action, data);
                return clients.openWindow(url);
            }
        })
    );
});

// Helper functions
function shouldCacheRequest(request) {
    const url = new URL(request.url);
    
    // Cache static assets
    return url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|ico|woff|woff2)$/) ||
           STATIC_CACHE_RESOURCES.includes(url.pathname);
}

function isAPIRequest(request) {
    const url = new URL(request.url);
    return url.pathname.startsWith('/api/');
}

function isNavigationRequest(request) {
    return request.mode === 'navigate' || 
           (request.method === 'GET' && request.headers.get('accept').includes('text/html'));
}

async function handleCacheableRequest(request) {
    try {
        // Try cache first (cache-first strategy)
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }

        // If not in cache, fetch from network
        const networkResponse = await fetch(request);
        
        // Cache successful responses
        if (networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('[ServiceWorker] Cache request failed:', error);
        
        // Return cached version if available
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (isNavigationRequest(request)) {
            return caches.match(OFFLINE_PAGE);
        }
        
        throw error;
    }
}

async function handleAPIRequest(request) {
    const url = new URL(request.url);
    
    try {
        // Try network first for API requests (network-first strategy)
        const networkResponse = await fetch(request);
        
        // Cache successful GET responses
        if (request.method === 'GET' && networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('[ServiceWorker] API request failed:', error);
        
        // For GET requests, try to return cached data
        if (request.method === 'GET') {
            const cachedResponse = await caches.match(request);
            if (cachedResponse) {
                // Add offline indicator to response
                const response = cachedResponse.clone();
                response.headers.set('X-Served-By', 'ServiceWorker');
                response.headers.set('X-Cache-Status', 'OFFLINE');
                return response;
            }
        }
        
        // Return offline response for critical endpoints
        if (shouldReturnOfflineData(url.pathname)) {
            return new Response(JSON.stringify({
                error: 'Offline',
                message: 'This data is not available offline',
                offline: true
            }), {
                status: 503,
                statusText: 'Service Unavailable',
                headers: { 'Content-Type': 'application/json' }
            });
        }
        
        throw error;
    }
}

async function handleNavigationRequest(request) {
    try {
        // Try network first
        const networkResponse = await fetch(request);
        return networkResponse;
    } catch (error) {
        console.error('[ServiceWorker] Navigation request failed:', error);
        
        // Return cached page if available
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page
        return caches.match(OFFLINE_PAGE);
    }
}

async function handleOfflinePost(request) {
    console.log('[ServiceWorker] Handling offline POST request');
    
    try {
        // Store the request for later sync
        const data = {
            url: request.url,
            method: request.method,
            headers: Object.fromEntries(request.headers.entries()),
            body: await request.text(),
            timestamp: Date.now()
        };
        
        // Store in IndexedDB for persistence
        await storeOfflineRequest(data);
        
        // Register for background sync
        await self.registration.sync.register('sync-offline-data');
        
        return new Response(JSON.stringify({
            success: true,
            message: 'Request stored for sync when online',
            offline: true
        }), {
            status: 202,
            statusText: 'Accepted',
            headers: { 'Content-Type': 'application/json' }
        });
    } catch (error) {
        console.error('[ServiceWorker] Error handling offline POST:', error);
        
        return new Response(JSON.stringify({
            error: 'Offline storage failed',
            message: 'Unable to store request for later sync'
        }), {
            status: 500,
            statusText: 'Internal Server Error',
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

function shouldReturnOfflineData(pathname) {
    const offlineEndpoints = [
        '/api/dashboard/metrics',
        '/api/patients',
        '/api/vitals',
        '/api/medications',
        '/api/appointments'
    ];
    
    return offlineEndpoints.some(endpoint => pathname.startsWith(endpoint));
}

// IndexedDB operations for offline storage
async function storeOfflineRequest(data) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('MCares-OfflineDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['requests'], 'readwrite');
            const store = transaction.objectStore('requests');
            
            store.add(data);
            
            transaction.oncomplete = () => resolve();
            transaction.onerror = () => reject(transaction.error);
        };
        
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            const store = db.createObjectStore('requests', { keyPath: 'id', autoIncrement: true });
            store.createIndex('timestamp', 'timestamp', { unique: false });
        };
    });
}

async function getOfflineRequests() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('MCares-OfflineDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['requests'], 'readonly');
            const store = transaction.objectStore('requests');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = () => resolve(getAllRequest.result);
            getAllRequest.onerror = () => reject(getAllRequest.error);
        };
    });
}

async function clearOfflineRequests() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('MCares-OfflineDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['requests'], 'readwrite');
            const store = transaction.objectStore('requests');
            
            store.clear();
            
            transaction.oncomplete = () => resolve();
            transaction.onerror = () => reject(transaction.error);
        };
    });
}

// Background sync operations
async function syncOfflineData() {
    console.log('[ServiceWorker] Syncing offline data');
    
    try {
        const requests = await getOfflineRequests();
        
        for (const requestData of requests) {
            try {
                const response = await fetch(requestData.url, {
                    method: requestData.method,
                    headers: requestData.headers,
                    body: requestData.body
                });
                
                if (response.ok) {
                    console.log('[ServiceWorker] Offline request synced:', requestData.url);
                } else {
                    console.error('[ServiceWorker] Sync failed for:', requestData.url, response.status);
                }
            } catch (error) {
                console.error('[ServiceWorker] Error syncing request:', error);
                throw error; // Re-throw to retry sync later
            }
        }
        
        await clearOfflineRequests();
        console.log('[ServiceWorker] All offline data synced successfully');
    } catch (error) {
        console.error('[ServiceWorker] Background sync failed:', error);
        throw error;
    }
}

async function syncVitalSigns() {
    console.log('[ServiceWorker] Syncing vital signs');
    // Implementation for syncing specific vital signs data
}

// Notification action handlers
async function handleNotificationAction(client, action, data) {
    switch (action) {
        case 'mark-taken':
            return client.postMessage({
                type: 'MEDICATION_TAKEN',
                data: data
            });
        case 'explore':
            return client.postMessage({
                type: 'NAVIGATE_TO_DETAILS',
                data: data
            });
        default:
            return client.focus();
    }
}

function getNotificationUrl(action, data) {
    switch (action) {
        case 'mark-taken':
            return '/patient/medications';
        case 'explore':
            return data.url || '/dashboard';
        default:
            return '/dashboard';
    }
}

// Periodic background sync (if supported)
self.addEventListener('periodicsync', (event) => {
    if (event.tag === 'sync-medical-data') {
        event.waitUntil(syncMedicalData());
    }
});

async function syncMedicalData() {
    console.log('[ServiceWorker] Periodic sync: medical data');
    
    // Sync critical medical data in background
    const criticalEndpoints = [
        '/api/emergency-alerts',
        '/api/critical-patients',
        '/api/medication-reminders'
    ];
    
    for (const endpoint of criticalEndpoints) {
        try {
            const response = await fetch(endpoint);
            if (response.ok) {
                const cache = await caches.open(CACHE_NAME);
                cache.put(endpoint, response.clone());
            }
        } catch (error) {
            console.error('[ServiceWorker] Failed to sync:', endpoint, error);
        }
    }
}

// Share target handling (for PWA share functionality)
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SHARE_TARGET') {
        event.waitUntil(handleShareTarget(event.data));
    }
});

async function handleShareTarget(data) {
    console.log('[ServiceWorker] Share target:', data);
    
    // Handle shared medical documents or reports
    const clients = await self.clients.matchAll({ type: 'window' });
    
    if (clients.length > 0) {
        clients[0].postMessage({
            type: 'SHARED_CONTENT',
            data: data
        });
        clients[0].focus();
    } else {
        // Open new window with shared content
        self.clients.openWindow('/upload?shared=true');
    }
}
