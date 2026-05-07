import { ref, computed } from 'vue'

export function useCache() {
  const cache = ref(new Map())
  const isSupported = ref('caches' in window)
  
  const set = async (key, value, options = {}) => {
    const {
      ttl = 3600000, // 1 hour default
      persist = false,
      version = '1.0'
    } = options
    
    const cacheItem = {
      value,
      timestamp: Date.now(),
      ttl,
      version
    }
    
    // Memory cache
    cache.value.set(key, cacheItem)
    
    // Persistent cache if enabled and supported
    if (persist && isSupported.value) {
      try {
        const cacheName = `reporte-ciudadanos-v${version}`
        const cacheStorage = await caches.open(cacheName)
        const response = new Response(JSON.stringify(cacheItem))
        await cacheStorage.put(key, response)
      } catch (error) {
        console.warn('Cache storage failed:', error)
      }
    }
  }
  
  const get = async (key, options = {}) => {
    const {
      persist = false,
      version = '1.0'
    } = options
    
    // Check memory cache first
    const memoryItem = cache.value.get(key)
    if (memoryItem && !isExpired(memoryItem)) {
      return memoryItem.value
    }
    
    // Check persistent cache if enabled
    if (persist && isSupported.value) {
      try {
        const cacheName = `reporte-ciudadanos-v${version}`
        const cacheStorage = await caches.open(cacheName)
        const response = await cacheStorage.match(key)
        
        if (response) {
          const cacheItem = await response.json()
          if (!isExpired(cacheItem)) {
            // Update memory cache
            cache.value.set(key, cacheItem)
            return cacheItem.value
          }
        }
      } catch (error) {
        console.warn('Cache retrieval failed:', error)
      }
    }
    
    return null
  }
  
  const remove = async (key, options = {}) => {
    const { persist = false, version = '1.0' } = options
    
    // Remove from memory cache
    cache.value.delete(key)
    
    // Remove from persistent cache
    if (persist && isSupported.value) {
      try {
        const cacheName = `reporte-ciudadanos-v${version}`
        const cacheStorage = await caches.open(cacheName)
        await cacheStorage.delete(key)
      } catch (error) {
        console.warn('Cache removal failed:', error)
      }
    }
  }
  
  const clear = async (options = {}) => {
    const { persist = false, version = '1.0' } = options
    
    // Clear memory cache
    cache.value.clear()
    
    // Clear persistent cache
    if (persist && isSupported.value) {
      try {
        const cacheName = `reporte-ciudadanos-v${version}`
        await caches.delete(cacheName)
      } catch (error) {
        console.warn('Cache clearing failed:', error)
      }
    }
  }
  
  const isExpired = (cacheItem) => {
    return Date.now() - cacheItem.timestamp > cacheItem.ttl
  }
  
  const cleanup = async (options = {}) => {
    const { persist = false, version = '1.0' } = options
    
    // Clean memory cache
    for (const [key, item] of cache.value.entries()) {
      if (isExpired(item)) {
        cache.value.delete(key)
      }
    }
    
    // Clean persistent cache
    if (persist && isSupported.value) {
      try {
        const cacheName = `reporte-ciudadanos-v${version}`
        const cacheStorage = await caches.open(cacheName)
        const keys = await cacheStorage.keys()
        
        for (const request of keys) {
          const response = await cacheStorage.match(request)
          if (response) {
            const cacheItem = await response.json()
            if (isExpired(cacheItem)) {
              await cacheStorage.delete(request)
            }
          }
        }
      } catch (error) {
        console.warn('Cache cleanup failed:', error)
      }
    }
  }
  
  const getStats = computed(() => {
    return {
      memorySize: cache.value.size,
      isSupported: isSupported.value
    }
  })
  
  // Auto cleanup expired items every 5 minutes
  const startAutoCleanup = () => {
    setInterval(() => {
      cleanup()
    }, 300000) // 5 minutes
  }
  
  return {
    cache,
    isSupported,
    set,
    get,
    remove,
    clear,
    cleanup,
    getStats,
    startAutoCleanup
  }
}

export function useApiCache() {
  const { set, get, remove, clear } = useCache()
  
  const cacheApiResponse = async (url, response, ttl = 300000) => { // 5 minutes default
    const cacheKey = `api:${url}`
    await set(cacheKey, {
      data: response,
      status: response.status,
      headers: Object.fromEntries(response.headers.entries())
    }, { ttl, persist: true })
  }
  
  const getCachedResponse = async (url) => {
    const cacheKey = `api:${url}`
    const cached = await get(cacheKey, { persist: true })
    
    if (cached) {
      return new Response(cached.data, {
        status: cached.status,
        headers: cached.headers
      })
    }
    
    return null
  }
  
  const invalidateCache = async (pattern) => {
    // This would need to be implemented based on your cache structure
    // For now, we'll clear the entire cache
    await clear({ persist: true })
  }
  
  return {
    cacheApiResponse,
    getCachedResponse,
    invalidateCache
  }
}
