const CACHE_NAME = "fukusuke-v1.0.5"; // Update version ini setiap ada perubahan
const filesToCache = ["/", "/offline.html"];

// Install event
self.addEventListener("install", function (event) {
    console.log("Service Worker installing...", CACHE_NAME);
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => cache.addAll(filesToCache))
            .then(() => self.skipWaiting())
    );
});

// Message event untuk handle update
self.addEventListener("message", (event) => {
    if (event.data && event.data.type === "SKIP_WAITING") {
        self.skipWaiting();
    }
});

// Activate event
self.addEventListener("activate", function (event) {
    console.log("Service Worker activating...");
    event.waitUntil(
        caches
            .keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME) {
                            console.log("Deleting old cache:", cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => self.clients.claim())
    );
});

// Fetch event
self.addEventListener("fetch", function (event) {
    const { request } = event;
    const url = request.url;

    // CRITICAL: Skip non-HTTP requests (chrome-extension, file://, dll)
    if (!url.startsWith("http://") && !url.startsWith("https://")) {
        return; // Biarkan browser handle
    }

    // Skip semua request POST/PUT/DELETE (untuk Livewire)
    if (request.method !== "GET") {
        return;
    }

    // Skip Livewire requests
    if (url.includes("/livewire/")) {
        return;
    }

    // Skip API dan dynamic routes
    if (
        url.includes("/api/") ||
        url.includes("/dashboard-infure") ||
        url.includes("/broadcasting/") ||
        url.includes("sanctum/csrf-cookie")
    ) {
        event.respondWith(fetch(request));
        return;
    }

    // Network First strategy untuk HTML pages
    if (request.headers.get("accept").includes("text/html")) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Clone response untuk cache
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    // Fallback ke cache atau offline page
                    return caches.match(request).then((cached) => {
                        return cached || caches.match("/offline.html");
                    });
                })
        );
        return;
    }

    // Cache First strategy untuk assets (CSS, JS, images, fonts)
    event.respondWith(
        caches
            .match(request)
            .then((cached) => {
                if (cached) {
                    return cached;
                }
                return fetch(request).then((response) => {
                    // Clone response untuk cache
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseClone);
                    });
                    return response;
                });
            })
            .catch(() => {
                // Jika offline dan tidak ada cache
                if (request.destination === "image") {
                    // Return placeholder image jika perlu
                    return new Response("", { status: 404 });
                }
                return new Response("Offline", { status: 503 });
            })
    );
});
