const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        // caching index and important routes
        return cache.addAll(filesToCache);
    });
};

self.addEventListener("install", function (event) {
    event.waitUntil(preLoad());
});

const filesToCache = ["/", "/offline.html"];

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

const addToCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        });
    });
};

const returnFromCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", function (event) {
    // TAMBAHKAN INI: Jangan cache API requests dan dashboard data
    const url = new URL(event.request.url);

    // Skip caching untuk:
    // - API calls
    // - Dashboard routes yang fetch data dinamis
    // - AJAX requests
    if (
        event.request.url.includes("/api/") ||
        event.request.url.includes("/dashboard") ||
        event.request.url.includes("/livewire/") ||
        event.request.method !== "GET"
    ) {
        // Fetch langsung tanpa cache
        event.respondWith(fetch(event.request));
        return;
    }

    // Untuk request lainnya, gunakan cache strategy
    event.respondWith(
        checkResponse(event.request).catch(function () {
            return returnFromCache(event.request);
        })
    );

    if (!event.request.url.startsWith("http")) {
        event.waitUntil(addToCache(event.request));
    }
});
