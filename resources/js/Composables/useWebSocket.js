import { ref, onMounted, onUnmounted } from 'vue'

export function useWebSocket(url, options = {}) {
  const {
    reconnectInterval = 3000,
    maxReconnectAttempts = 5,
    heartbeatInterval = 30000,
    onMessage = null,
    onOpen = null,
    onClose = null,
    onError = null
  } = options

  const socket = ref(null)
  const isConnected = ref(false)
  const isReconnecting = ref(false)
  const reconnectAttempts = ref(0)
  const lastError = ref(null)
  const heartbeatTimer = ref(null)
  const messageQueue = ref([])
  const isConnecting = ref(false)

  const connect = () => {
    if (isConnecting.value || socket.value?.readyState === WebSocket.OPEN) {
      return
    }

    try {
      isConnecting.value = true
      console.log(`Attempting to connect to WebSocket: ${url}`)
      
      // Close existing connection if any
      if (socket.value) {
        socket.value.close()
      }

      // Create new WebSocket connection
      socket.value = new WebSocket(url)
      
      socket.value.onopen = (event) => {
        console.log('WebSocket connected', event)
        isConnected.value = true
        isConnecting.value = false
        isReconnecting.value = false
        reconnectAttempts.value = 0
        lastError.value = null
        
        // Send queued messages
        while (messageQueue.value.length > 0) {
          const message = messageQueue.value.shift()
          send(message)
        }
        
        // Start heartbeat
        startHeartbeat()
        
        if (onOpen) {
          onOpen(event)
        }
      }

      socket.value.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)
          
          // Handle heartbeat response
          if (data.type === 'pong') {
            return
          }
          
          if (onMessage) {
            onMessage(data, event)
          }
        } catch (error) {
          console.error('Error parsing WebSocket message:', error)
        }
      }

      socket.value.onclose = (event) => {
        console.log('WebSocket disconnected', event)
        isConnected.value = false
        isConnecting.value = false
        stopHeartbeat()
        
        if (onClose) {
          onClose(event)
        }

        // Attempt reconnection if not a normal closure and not already reconnecting
        if (!event.wasClean && !isReconnecting.value && reconnectAttempts.value < maxReconnectAttempts) {
          scheduleReconnect()
        }
      }

      socket.value.onerror = (event) => {
        console.error('WebSocket error:', event)
        lastError.value = event
        isConnected.value = false
        isConnecting.value = false
        
        if (onError) {
          onError(event)
        }
      }

    } catch (error) {
      console.error('Failed to create WebSocket connection:', error)
      lastError.value = error
      isConnecting.value = false
      
      if (reconnectAttempts.value < maxReconnectAttempts) {
        scheduleReconnect()
      }
    }
  }

  const disconnect = () => {
    if (socket.value) {
      socket.value.close(1000, 'Client disconnect')
    }
    stopHeartbeat()
    isConnected.value = false
    isConnecting.value = false
    isReconnecting.value = false
    reconnectAttempts.value = 0
    messageQueue.value = []
  }

  const send = (data) => {
    if (socket.value?.readyState === WebSocket.OPEN) {
      try {
        const message = typeof data === 'string' ? data : JSON.stringify(data)
        socket.value.send(message)
      } catch (error) {
        console.error('Error sending WebSocket message:', error)
        lastError.value = error
      }
    } else {
      // Queue message if not connected
      messageQueue.value.push(data)
      console.warn('WebSocket not connected, message queued:', data)
    }
  }

  const scheduleReconnect = () => {
    if (isReconnecting.value) return
    
    isReconnecting.value = true
    reconnectAttempts.value++
    
    console.log(`Scheduling reconnect attempt ${reconnectAttempts.value} in ${reconnectInterval}ms`)
    
    setTimeout(() => {
      if (reconnectAttempts.value <= maxReconnectAttempts) {
        connect()
      } else {
        console.error('Max reconnection attempts reached')
        isReconnecting.value = false
      }
    }, reconnectInterval)
  }

  const startHeartbeat = () => {
    stopHeartbeat()
    
    heartbeatTimer.value = setInterval(() => {
      if (socket.value?.readyState === WebSocket.OPEN) {
        send({ type: 'ping', timestamp: Date.now() })
      }
    }, heartbeatInterval)
  }

  const stopHeartbeat = () => {
    if (heartbeatTimer.value) {
      clearInterval(heartbeatTimer.value)
      heartbeatTimer.value = null
    }
  }

  const getConnectionState = () => {
    if (!socket.value) return 'DISCONNECTED'
    
    switch (socket.value.readyState) {
      case WebSocket.CONNECTING:
        return 'CONNECTING'
      case WebSocket.OPEN:
        return 'OPEN'
      case WebSocket.CLOSING:
        return 'CLOSING'
      case WebSocket.CLOSED:
        return 'CLOSED'
      default:
        return 'UNKNOWN'
    }
  }

  // Cleanup on unmount
  const cleanup = () => {
    disconnect()
  }

  return {
    socket,
    isConnected,
    isConnecting,
    isReconnecting,
    reconnectAttempts,
    lastError,
    connect,
    disconnect,
    send,
    getConnectionState,
    cleanup
  }
}

