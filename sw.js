const CACHE_NAME = 'ufts-cache-v3';

// Assets to cache immediately on install
const STATIC_ASSETS = [
    '/finance-tracker/public/css/style.css',
    '/finance-tracker/public/icons/icon-192x192.png',
    '/finance-tracker/public/icons/icon-512x512.png',
    '/finance-tracker/manifest.json'
];

self.addEventListener('install', (event) => {
    // Skip waiting so the new service worker activates immediately
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Opened cache');
            return cache.addAll(STATIC_ASSETS);
        })
    );
});

self.addEventListener('activate', (event) => {
    // Claim clients to immediately control all tabs
    event.waitUntil(self.clients.claim());
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter((name) => name !== CACHE_NAME).map((name) => {
                    return caches.delete(name);
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    // Only cache GET requests. Ignore POST requests (like form submissions or AJAX).
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    
    // If it's a CSS, JS, or image file (Cache-first strategy)
    if (url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|gif|woff|woff2|ttf|eot)$/)) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request).then((fetchResponse) => {
                    return caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, fetchResponse.clone());
                        return fetchResponse;
                    });
                }).catch(() => {
                    // Fallback for static assets if offline
                    return new Response('', { status: 404 });
                });
            })
        );
    } 
    // If it's a PHP page or main route (Network-first strategy)
    else {
        event.respondWith(
            fetch(event.request).then((response) => {
                // Return fresh data if network works, and cache it
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseClone);
                });
                return response;
            }).catch(() => {
                // If offline, try to return from cache
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    
                    // IF THE PAGE IS NOT IN CACHE, RETURN A PROPER FALLBACK RESPONSE
                    // This prevents the "Uncaught (in promise) TypeError: Failed to convert value to 'Response'" error
                    return new Response(
                        '<div style="font-family: sans-serif; text-align: center; padding: 40px; background: #0f172a; color: white; height: 100vh; position: fixed; inset: 0;"><h2>You are offline</h2><p>This page hasn\'t been cached yet. Please reconnect to the internet to view it.</p></div>',
                        {
                            status: 503,
                            statusText: 'Service Unavailable',
                            headers: new Headers({
                                'Content-Type': 'text/html'
                            })
                        }
                    );
                });
            })
        );
    }
});
