import { ref, createApp } from 'vue'
import Toast from '@/Components/Toast.vue'

const toastQueue = ref([])
const currentToast = ref(null)

export function useToast() {
  const showToast = (options) => {
    const toastId = Date.now() + Math.random()
    
    const toastData = {
      id: toastId,
      ...options,
      resolve: null
    }
    
    return new Promise((resolve) => {
      toastData.resolve = resolve
      toastQueue.value.push(toastData)
      processQueue()
    })
  }
  
  const success = (message, options = {}) => {
    return showToast({ type: 'success', message, ...options })
  }
  
  const error = (message, options = {}) => {
    return showToast({ type: 'error', message, ...options })
  }
  
  const warning = (message, options = {}) => {
    return showToast({ type: 'warning', message, ...options })
  }
  
  const info = (message, options = {}) => {
    return showToast({ type: 'info', message, ...options })
  }
  
  const processQueue = () => {
    if (currentToast.value || toastQueue.value.length === 0) {
      return
    }
    
    const toastData = toastQueue.value.shift()
    currentToast.value = toastData
    
    // Create toast container if it doesn't exist
    let container = document.getElementById('toast-container')
    if (!container) {
      container = document.createElement('div')
      container.id = 'toast-container'
      container.className = 'fixed top-4 right-4 z-50 pointer-events-none'
      document.body.appendChild(container)
    }
    
    // Create toast instance
    const toastElement = document.createElement('div')
    toastElement.className = 'pointer-events-auto'
    container.appendChild(toastElement)
    
    const toastApp = createApp(Toast, {
      ...toastData,
      onClose: () => {
        toastApp.unmount()
        toastElement.remove()
        currentToast.value = null
        if (toastData.resolve) {
          toastData.resolve()
        }
        // Process next toast in queue
        setTimeout(processQueue, 100)
      }
    })
    
    toastApp.mount(toastElement)
  }
  
  return {
    showToast,
    success,
    error,
    warning,
    info
  }
}
