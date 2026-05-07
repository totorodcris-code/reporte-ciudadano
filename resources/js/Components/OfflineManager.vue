<template>
  <div class="fixed bottom-4 right-4 z-50">
    <div
      v-if="showOfflineBanner"
      class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-lg p-4 max-w-sm"
    >
      <div class="flex items-center gap-3">
        <div class="flex-shrink-0">
          <i class="fas fa-wifi-slash text-yellow-600 text-xl"></i>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-semibold text-yellow-800">Modo Sin Conexión</h4>
          <p class="text-xs text-yellow-700">Estás trabajando sin conexión. Los cambios se sincronizarán cuando te reconectes.</p>
        </div>
        <button
          @click="hideOfflineBanner"
          class="flex-shrink-0 text-yellow-600 hover:text-yellow-800"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    
    <!-- Sync Status Indicator -->
    <div
      v-if="showSyncStatus"
      class="bg-white border border-gray-200 rounded-lg shadow-lg p-3 max-w-xs"
    >
      <div class="flex items-center gap-3">
        <div class="flex-shrink-0">
          <div
            class="w-3 h-3 rounded-full"
            :class="syncStatusClass"
          ></div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900">{{ syncStatusText }}</p>
          <p class="text-xs text-gray-500">{{ pendingChanges }} cambios pendientes</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'

export default {
  name: 'OfflineManager',
  setup() {
    const isOnline = ref(navigator.onLine)
    const showOfflineBanner = ref(false)
    const showSyncStatus = ref(false)
    const pendingChanges = ref(0)
    const isSyncing = ref(false)
    const syncQueue = ref([])
    
    const syncStatusClass = computed(() => {
      if (isSyncing.value) return 'bg-blue-500 animate-pulse'
      if (pendingChanges.value > 0) return 'bg-yellow-500'
      return 'bg-green-500'
    })
    
    const syncStatusText = computed(() => {
      if (isSyncing.value) return 'Sincronizando...'
      if (pendingChanges.value > 0) return 'Pendiente de sincronización'
      return 'Sincronizado'
    })
    
    const handleOnline = () => {
      isOnline.value = true
      showOfflineBanner.value = false
      showSyncStatus.value = false
      
      // Start syncing queued changes
      if (syncQueue.value.length > 0) {
        syncPendingChanges()
      }
    }
    
    const handleOffline = () => {
      isOnline.value = false
      showOfflineBanner.value = true
      showSyncStatus.value = true
    }
    
    const addToSyncQueue = (action, data) => {
      const syncItem = {
        id: Date.now(),
        action,
        data,
        timestamp: new Date().toISOString(),
        retries: 0
      }
      
      syncQueue.value.push(syncItem)
      pendingChanges.value = syncQueue.value.length
      
      // Save to localStorage for persistence
      saveSyncQueue()
    }
    
    const syncPendingChanges = async () => {
      if (!isOnline.value || isSyncing.value) return
      
      isSyncing.value = true
      
      try {
        // Process sync queue
        const failedItems = []
        
        for (const item of syncQueue.value) {
          try {
            // Simulate API call
            await performSyncAction(item.action, item.data)
          } catch (error) {
            console.error('Sync failed for item:', item, error)
            item.retries++
            
            if (item.retries < 3) {
              failedItems.push(item)
            } else {
              console.error('Max retries exceeded for item:', item)
            }
          }
        }
        
        syncQueue.value = failedItems
        pendingChanges.value = syncQueue.value.length
        
        // Update localStorage
        saveSyncQueue()
        
        if (failedItems.length === 0) {
          showSyncStatus.value = false
        }
        
      } catch (error) {
        console.error('Sync process failed:', error)
      } finally {
        isSyncing.value = false
      }
    }
    
    const performSyncAction = async (action, data) => {
      // Simulate network delay
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Simulate API calls
      switch (action) {
        case 'create_incidencia':
          console.log('Syncing create incidencia:', data)
          break
        case 'update_incidencia':
          console.log('Syncing update incidencia:', data)
          break
        case 'delete_incidencia':
          console.log('Syncing delete incidencia:', data)
          break
        case 'create_comment':
          console.log('Syncing create comment:', data)
          break
        default:
          console.log('Unknown sync action:', action, data)
      }
    }
    
    const saveSyncQueue = () => {
      try {
        localStorage.setItem('syncQueue', JSON.stringify(syncQueue.value))
      } catch (error) {
        console.error('Failed to save sync queue:', error)
      }
    }
    
    const loadSyncQueue = () => {
      try {
        const saved = localStorage.getItem('syncQueue')
        if (saved) {
          syncQueue.value = JSON.parse(saved)
          pendingChanges.value = syncQueue.value.length
        }
      } catch (error) {
        console.error('Failed to load sync queue:', error)
      }
    }
    
    const hideOfflineBanner = () => {
      showOfflineBanner.value = false
    }
    
    const clearSyncQueue = () => {
      syncQueue.value = []
      pendingChanges.value = 0
      saveSyncQueue()
      showSyncStatus.value = false
    }
    
    // Service Worker registration for offline functionality
    const registerServiceWorker = async () => {
      if ('serviceWorker' in navigator) {
        try {
          const registration = await navigator.serviceWorker.register('/sw.js')
          console.log('Service Worker registered:', registration)
          
          // Listen for messages from service worker
          navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data.type === 'sync-complete') {
              syncPendingChanges()
            }
          })
          
        } catch (error) {
          console.error('Service Worker registration failed:', error)
        }
      }
    }
    
    // Cache management
    const cacheData = async (key, data) => {
      try {
        const cache = await caches.open('offline-cache-v1')
        const response = new Response(JSON.stringify(data))
        await cache.put(key, response)
      } catch (error) {
        console.error('Failed to cache data:', error)
      }
    }
    
    const getCachedData = async (key) => {
      try {
        const cache = await caches.open('offline-cache-v1')
        const response = await cache.match(key)
        if (response) {
          return await response.json()
        }
      } catch (error) {
        console.error('Failed to get cached data:', error)
      }
      return null
    }
    
    const clearCache = async () => {
      try {
        const cacheNames = await caches.keys()
        await Promise.all(cacheNames.map(name => caches.delete(name)))
        console.log('All caches cleared')
      } catch (error) {
        console.error('Failed to clear caches:', error)
      }
    }
    
    onMounted(() => {
      // Load sync queue from localStorage
      loadSyncQueue()
      
      // Register service worker
      registerServiceWorker()
      
      // Add event listeners
      window.addEventListener('online', handleOnline)
      window.addEventListener('offline', handleOffline)
      
      // Show sync status if there are pending changes
      if (pendingChanges.value > 0) {
        showSyncStatus.value = true
      }
      
      // Sync when coming online
      if (isOnline.value && syncQueue.value.length > 0) {
        setTimeout(() => syncPendingChanges(), 1000)
      }
    })
    
    onUnmounted(() => {
      window.removeEventListener('online', handleOnline)
      window.removeEventListener('offline', handleOffline)
    })
    
    return {
      isOnline,
      showOfflineBanner,
      showSyncStatus,
      pendingChanges,
      syncStatusClass,
      syncStatusText,
      addToSyncQueue,
      hideOfflineBanner,
      clearSyncQueue,
      cacheData,
      getCachedData,
      clearCache
    }
  }
}
</script>
