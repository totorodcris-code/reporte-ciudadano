const CACHE_NAME = 'offline-cache-v1'
const urlsToCache = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/images/no-image.png',
  '/favicon.ico'
]

// Install service worker
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Opened cache')
        return cache.addAll(urlsToCache)
      })
  )
})

// Fetch event handler
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Return cached version or fetch from network
        if (response) {
          return response
        }
        
        return fetch(event.request).then((response) => {
          // Check if valid response
          if (!response || response.status !== 200 || response.type !== 'basic') {
            return response
          }
          
          // Clone response since it can only be used once
          const responseToCache = response.clone()
          
          caches.open(CACHE_NAME)
            .then((cache) => {
              cache.put(event.request, responseToCache)
            })
          
          return response
        }).catch(() => {
          // Return offline page for navigation requests
          if (event.request.mode === 'navigate') {
            return caches.match('/offline.html')
          }
        })
      })
  )
})

// Activate service worker
self.addEventListener('activate', (event) => {
  const cacheWhitelist = [CACHE_NAME]
  
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            return caches.delete(cacheName)
          }
        })
      )
    })
  )
})

// Background sync
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-pending-changes') {
    event.waitUntil(syncPendingChanges())
  }
})

// Push notifications
self.addEventListener('push', (event) => {
  const options = {
    body: event.data.text(),
    icon: '/favicon.ico',
    badge: '/favicon.ico',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  }
  
  event.waitUntil(
    self.registration.showNotification('ReporteCiudadanos', options)
  )
})

// Notification click
self.addEventListener('notificationclick', (event) => {
  event.notification.close()
  
  event.waitUntil(
    clients.openWindow('/')
  )
})

async function syncPendingChanges() {
  try {
    // Get sync queue from IndexedDB or localStorage
    const syncQueue = await getSyncQueue()
    
    for (const item of syncQueue) {
      try {
        await performSyncAction(item.action, item.data)
        await removeSyncItem(item.id)
      } catch (error) {
        console.error('Sync failed for item:', item, error)
      }
    }
    
    // Notify client about sync completion
    const clients = await self.clients.matchAll()
    clients.forEach(client => {
      client.postMessage({ type: 'sync-complete' })
    })
    
  } catch (error) {
    console.error('Sync process failed:', error)
  }
}

async function performSyncAction(action, data) {
  // Simulate API calls
  switch (action) {
    case 'create_incidencia':
      return await fetch('/api/incidencias', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
    case 'update_incidencia':
      return await fetch(`/api/incidencias/${data.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
    case 'delete_incidencia':
      return await fetch(`/api/incidencias/${data.id}`, {
        method: 'DELETE'
      })
    default:
      throw new Error('Unknown sync action:', action)
  }
}

// Helper functions for IndexedDB
async function getSyncQueue() {
  // This would typically use IndexedDB for persistence
  return []
}

async function removeSyncItem(id) {
  // Remove item from sync queue
  console.log('Removing sync item:', id)
}
