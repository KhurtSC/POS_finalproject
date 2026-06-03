const CACHE_NAME = 'pointsale-pos-v1';
const CORE_ASSETS = [
    '/',
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/pos.js',
    '/assets/js/charts.js',
    '/assets/images/logo.png',
    '/assets/images/brand/pointsale-logo.svg',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(CORE_ASSETS))
            .catch(() => null)
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
        ))
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') return;
    if (new URL(request.url).pathname.startsWith('/api/')) return;

    event.respondWith(
        fetch(request)
            .then((response) => {
                const copy = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                return response;
            })
            .catch(() => caches.match(request))
    );
});
