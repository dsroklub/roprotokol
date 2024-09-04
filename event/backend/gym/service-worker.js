self.addEventListener('install', event => {  console.log('Service Worker installing.');});
self.addEventListener('fetch', event => {  console.log('Service Worker fetching.', event.request.url); });
