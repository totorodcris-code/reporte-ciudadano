<template>
  <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Colaboración en Tiempo Real</h3>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full" :class="isConnected ? 'bg-green-500' : 'bg-red-500'"></div>
        <span class="text-sm text-gray-600">{{ isConnected ? 'Conectado' : 'Desconectado' }}</span>
      </div>
    </div>
    
    <!-- Active users -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-800 mb-3">Usuarios Activos</h4>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
        <div
          v-for="user in activeUsers"
          :key="user.id"
          class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg"
        >
          <div class="relative">
            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div v-if="user.isTyping" class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ user.role }}</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Chat area -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-800 mb-3">Chat de Equipo</h4>
      <div class="border border-gray-200 rounded-lg p-4 h-64 overflow-y-auto mb-4 bg-gray-50">
        <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
          <i class="fas fa-comments text-4xl mb-3"></i>
          <p>No hay mensajes aún</p>
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="message in messages"
            :key="message.id"
            class="flex gap-3"
            :class="{ 'flex-row-reverse': message.userId === currentUserId }"
          >
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                {{ message.userName.charAt(0).toUpperCase() }}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-sm font-medium text-gray-900">{{ message.userName }}</span>
                <span class="text-xs text-gray-500">{{ formatTime(message.timestamp) }}</span>
              </div>
              <div
                class="px-3 py-2 rounded-lg text-sm"
                :class="message.userId === currentUserId ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
              >
                {{ message.content }}
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Message input -->
      <div class="flex gap-2">
        <input
          v-model="newMessage"
          type="text"
          placeholder="Escribe un mensaje..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          @keydown.enter="sendMessage"
        />
        <button
          @click="sendMessage"
          :disabled="!newMessage.trim() || !isConnected"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200"
        >
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>
    </div>
    
    <!-- Shared cursor -->
    <div v-if="sharedCursor" class="mb-6">
      <h4 class="text-md font-medium text-gray-800 mb-3">Cursor Compartido</h4>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
        <div class="flex items-center gap-2">
          <i class="fas fa-mouse-pointer text-blue-600"></i>
          <span class="text-sm text-blue-800">
            {{ sharedCursor.userName }} está viendo: {{ sharedCursor.element }}
          </span>
        </div>
      </div>
    </div>
    
    <!-- Activity feed -->
    <div>
      <h4 class="text-md font-medium text-gray-800 mb-3">Actividad Reciente</h4>
      <div class="space-y-2 max-h-48 overflow-y-auto">
        <div
          v-for="activity in recentActivity"
          :key="activity.id"
          class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition-colors duration-200"
        >
          <i :class="activity.icon + ' text-gray-400'"></i>
          <div class="flex-1 min-w-0">
            <p class="text-sm text-gray-900">{{ activity.description }}</p>
            <span class="text-xs text-gray-500">{{ formatTime(activity.timestamp) }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useWebSocket } from '@/Composables/useWebSocket'

export default {
  name: 'CollaborationPanel',
  setup() {
    const { isConnected, send } = useWebSocket('ws://localhost:8000/collaboration', {
      onMessage: handleCollaborationMessage
    })
    
    const activeUsers = ref([])
    const messages = ref([])
    const newMessage = ref('')
    const sharedCursor = ref(null)
    const recentActivity = ref([])
    const currentUserId = ref('user-1') // This would come from auth store
    
    const handleCollaborationMessage = (data) => {
      switch (data.type) {
        case 'user_joined':
          activeUsers.value = data.users
          addActivity(`${data.user.name} se unió a la sesión`, 'user-plus-circle')
          break
        case 'user_left':
          activeUsers.value = activeUsers.value.filter(u => u.id !== data.user.id)
          addActivity(`${data.user.name} abandonó la sesión`, 'user-minus-circle')
          break
        case 'user_typing':
          const user = activeUsers.value.find(u => u.id === data.userId)
          if (user) {
            user.isTyping = true
            setTimeout(() => {
              user.isTyping = false
            }, 3000)
          }
          break
        case 'chat_message':
          messages.value.push({
            id: Date.now(),
            userId: data.userId,
            userName: data.userName,
            content: data.content,
            timestamp: new Date().toISOString()
          })
          break
        case 'cursor_move':
          sharedCursor.value = {
            userId: data.userId,
            userName: data.userName,
            element: data.element,
            x: data.x,
            y: data.y
          }
          break
        case 'activity':
          addActivity(data.description, data.icon)
          break
      }
    }
    
    const sendMessage = () => {
      if (newMessage.value.trim() && isConnected.value) {
        send({
          type: 'chat_message',
          content: newMessage.value.trim(),
          userId: currentUserId.value
        })
        
        messages.value.push({
          id: Date.now(),
          userId: currentUserId.value,
          userName: 'Tú',
          content: newMessage.value.trim(),
          timestamp: new Date().toISOString()
        })
        
        newMessage.value = ''
      }
    }
    
    const addActivity = (description, icon = 'fa-info-circle') => {
      recentActivity.value.unshift({
        id: Date.now(),
        description,
        icon,
        timestamp: new Date().toISOString()
      })
      
      if (recentActivity.value.length > 20) {
        recentActivity.value = recentActivity.value.slice(0, 20)
      }
    }
    
    const formatTime = (timestamp) => {
      const date = new Date(timestamp)
      const now = new Date()
      const diffMs = now - date
      const diffMins = Math.floor(diffMs / 60000)
      
      if (diffMins < 1) {
        return 'Ahora'
      } else if (diffMins < 60) {
        return `Hace ${diffMins} min`
      } else {
        return date.toLocaleTimeString()
      }
    }
    
    onMounted(() => {
      // Simulate initial data
      activeUsers.value = [
        { id: 'user-1', name: 'Juan Pérez', role: 'Administrador', isTyping: false },
        { id: 'user-2', name: 'María García', role: 'Editor', isTyping: false },
        { id: 'user-3', name: 'Carlos López', role: 'Viewer', isTyping: false }
      ]
      
      recentActivity.value = [
        {
          id: 1,
          description: 'Juan Pérez creó una nueva incidencia',
          icon: 'fa-plus-circle',
          timestamp: new Date(Date.now() - 300000).toISOString()
        },
        {
          id: 2,
          description: 'María García actualizó el reporte #1234',
          icon: 'fa-edit',
          timestamp: new Date(Date.now() - 600000).toISOString()
        }
      ]
    })
    
    return {
      isConnected,
      activeUsers,
      messages,
      newMessage,
      sharedCursor,
      recentActivity,
      currentUserId,
      sendMessage,
      formatTime
    }
  }
}
</script>
