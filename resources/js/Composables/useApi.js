import { ref } from 'vue'

export function useApi() {
  const isRetrying = ref(false)
  const retryCount = ref(0)
  const maxRetries = 3
  const retryDelay = 1000 // 1 second

  const createAbortController = () => {
    return new AbortController()
  }

  const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms))

  const fetchWithRetry = async (url, options = {}) => {
    const controller = createAbortController()
    const { timeout = 10000, retries = maxRetries, ...fetchOptions } = options
    
    const attempt = async (attemptNumber = 1) => {
      try {
        isRetrying.value = true
        retryCount.value = attemptNumber - 1

        const response = await fetch(url, {
          ...fetchOptions,
          signal: controller.signal,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Cache-Control': 'no-cache',
            ...fetchOptions.headers
          }
        })

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`)
        }

        isRetrying.value = false
        retryCount.value = 0
        return response
      } catch (error) {
        console.error(`API Error (attempt ${attemptNumber}/${retries}):`, error)

        // Don't retry on certain errors
        if (
          error.name === 'AbortError' ||
          error.message?.includes('Failed to fetch') ||
          attemptNumber >= retries
        ) {
          isRetrying.value = false
          throw error
        }

        // Wait before retrying
        await sleep(retryDelay * attemptNumber)
        return attempt(attemptNumber + 1)
      }
    }

    // Set timeout
    if (timeout) {
      setTimeout(() => controller.abort(), timeout)
    }

    return attempt()
  }

  const get = async (url, options = {}) => {
    return fetchWithRetry(url, { ...options, method: 'GET' })
  }

  const post = async (url, data, options = {}) => {
    return fetchWithRetry(url, {
      ...options,
      method: 'POST',
      body: JSON.stringify(data)
    })
  }

  const put = async (url, data, options = {}) => {
    return fetchWithRetry(url, {
      ...options,
      method: 'PUT',
      body: JSON.stringify(data)
    })
  }

  const del = async (url, options = {}) => {
    return fetchWithRetry(url, { ...options, method: 'DELETE' })
  }

  const upload = async (url, formData, options = {}) => {
    return fetchWithRetry(url, {
      ...options,
      method: 'POST',
      body: formData,
      headers: {
        // Don't set Content-Type for FormData (browser sets it with boundary)
        ...options.headers
      }
    })
  }

  // Specific API methods for the application
  const api = {
    // Auth endpoints
    login: async (credentials) => {
      return post('/api/login', credentials)
    },

    register: async (userData) => {
      return post('/api/register', userData)
    },

    logout: async () => {
      return post('/api/logout')
    },

    // Incidencias endpoints
    getIncidencias: async (params = {}) => {
      const queryString = new URLSearchParams(params).toString()
      return get(`/api/incidencias?${queryString}`)
    },

    getIncidencia: async (id) => {
      return get(`/api/incidencias/${id}`)
    },

    createIncidencia: async (formData) => {
      return upload('/api/incidencias', formData)
    },

    updateIncidencia: async (id, data) => {
      return put(`/api/incidencias/${id}`, data)
    },

    deleteIncidencia: async (id) => {
      return del(`/api/incidencias/${id}`)
    },

    // Estadísticas endpoints
    getEstadisticas: async () => {
      return get('/api/estadisticas')
    },

    getReportesDestacados: async () => {
      return get('/api/reportes-destacados')
    },

    // Categorías endpoints
    getCategorias: async () => {
      return get('/api/categorias')
    },

    // Usuarios endpoints
    getPerfil: async () => {
      return get('/api/perfil')
    },

    updatePerfil: async (data) => {
      return put('/api/perfil', data)
    },

    // Admin endpoints
    getUsuarios: async (params = {}) => {
      const queryString = new URLSearchParams(params).toString()
      return get(`/api/admin/usuarios?${queryString}`)
    },

    createUsuario: async (userData) => {
      return post('/api/admin/usuarios', userData)
    },

    updateUsuario: async (id, userData) => {
      return put(`/api/admin/usuarios/${id}`, userData)
    },

    deleteUsuario: async (id) => {
      return del(`/api/admin/usuarios/${id}`)
    }
  }

  return {
    isRetrying,
    retryCount,
    fetchWithRetry,
    get,
    post,
    put,
    del,
    upload,
    api
  }
}
