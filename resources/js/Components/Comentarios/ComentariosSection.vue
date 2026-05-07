<template>
  <div :class="compact ? 'space-y-2' : 'bg-white rounded-xl shadow-lg p-6 border border-gray-200'">
    <!-- Modo compacto -->
    <div v-if="compact" class="space-y-2">
      <!-- Botón de comentarios compacto -->
      <div class="flex items-center justify-between">
        <button
          @click="toggleForm"
          class="flex items-center gap-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 text-sm"
        >
          <i class="fas fa-comment"></i>
          <span>{{ comentarios.length }} comentarios</span>
        </button>
        
        <button
          v-if="authStore.isLoggedIn"
          @click="toggleForm"
          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
        >
          <i class="fas fa-plus mr-1"></i>
          Comentar
        </button>
        <button
          v-else
          @click="$router.push('/login')"
          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
        >
          <i class="fas fa-login mr-1"></i>
          Inicia para comentar
        </button>
      </div>

      <!-- Formulario compacto -->
      <div v-if="showForm" class="p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
        <textarea
          v-model="nuevoComentario.contenido"
          rows="1"
          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white text-sm resize-none"
          placeholder="Escribe tu comentario..."
          @keyup.enter="enviarComentario"
        ></textarea>
        <div class="flex gap-1 mt-1">
          <button
            @click="enviarComentario"
            :disabled="loading || !nuevoComentario.contenido.trim()"
            class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 transition-colors text-xs"
          >
            {{ loading ? '...' : 'Enviar' }}
          </button>
          <button
            @click="cancelarForm"
            class="px-2 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors text-xs"
          >
            X
          </button>
        </div>
      </div>

      <!-- Lista compacta de comentarios -->
      <div v-if="comentarios.length > 0" class="space-y-1 max-h-32 overflow-y-auto">
        <div
          v-for="comentario in comentarios.slice(0, 3)"
          :key="comentario.id"
          class="p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded text-sm"
        >
          <div class="flex items-start gap-2">
            <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
              {{ getInitials(comentario.user.name) }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <span class="font-medium text-gray-900 dark:text-white text-xs">{{ comentario.user.name }}</span>
                <span class="text-xs text-gray-500">{{ formatDate(comentario.created_at) }}</span>
              </div>
              <p class="text-gray-800 dark:text-gray-200 text-xs line-clamp-2">{{ comentario.contenido }}</p>
            </div>
          </div>
        </div>
        
        <div v-if="comentarios.length > 3" class="text-center text-xs text-gray-500 dark:text-gray-400">
          <router-link 
            :to="`/incidencias/${incidenciaId}`"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
          >
            Ver todos los {{ comentarios.length }} comentarios →
          </router-link>
        </div>
      </div>
    </div>

    <!-- Modo completo -->
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900">
          <i class="fas fa-comments text-blue-600 mr-2"></i>
          Comentarios ({{ comentarios.length }})
        </h3>
        <button
          @click="toggleForm"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          <i class="fas fa-plus mr-2"></i>
          Añadir Comentario
        </button>
      </div>

      <!-- Formulario de nuevo comentario -->
      <div v-if="showForm" class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Tu comentario
          </label>
          <textarea
            v-model="nuevoComentario.contenido"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Escribe tu comentario aquí..."
          ></textarea>
        </div>
        <div class="flex gap-2">
          <button
            @click="enviarComentario"
            :disabled="loading || !nuevoComentario.contenido.trim()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
          >
            <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
            {{ loading ? 'Enviando...' : 'Enviar Comentario' }}
          </button>
          <button
            @click="cancelarForm"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>

      <!-- Lista de comentarios -->
      <div v-if="loading && !showForm" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <p class="ml-4 text-gray-600">Cargando comentarios...</p>
      </div>

      <div v-else-if="comentarios.length === 0" class="text-center py-8 text-gray-500">
        <i class="fas fa-comments text-4xl mb-4"></i>
        <p>No hay comentarios aún. Sé el primero en comentar.</p>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="comentario in comentarios"
          :key="comentario.id"
          class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <!-- Comentario principal -->
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3">
                  {{ getInitials(comentario.user.name) }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ comentario.user.name }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(comentario.created_at) }}</p>
                </div>
              </div>
              <p class="text-gray-700 mb-3">{{ comentario.contenido }}</p>
              
              <!-- Acciones -->
              <div class="flex items-center gap-4 text-sm">
                <button
                  @click="responderComentario(comentario)"
                  class="text-blue-600 hover:text-blue-800 transition-colors"
                >
                  <i class="fas fa-reply mr-1"></i>
                  Responder
                </button>
                <button
                  v-if="canEdit(comentario)"
                  @click="editarComentario(comentario)"
                  class="text-green-600 hover:text-green-800 transition-colors"
                >
                  <i class="fas fa-edit mr-1"></i>
                  Editar
                </button>
                <button
                  v-if="canDelete(comentario)"
                  @click="eliminarComentario(comentario)"
                  class="text-red-600 hover:text-red-800 transition-colors"
                >
                  <i class="fas fa-trash mr-1"></i>
                  Eliminar
                </button>
              </div>
            </div>
          </div>

          <!-- Formulario de respuesta -->
          <div v-if="replyingTo === comentario.id" class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
            <textarea
              v-model="respuestaContenido"
              rows="2"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3"
              placeholder="Escribe tu respuesta..."
            ></textarea>
            <div class="flex gap-2">
              <button
                @click="enviarRespuesta(comentario)"
                :disabled="loading || !respuestaContenido.trim()"
                class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors text-sm"
              >
                {{ loading ? 'Enviando...' : 'Enviar' }}
              </button>
              <button
                @click="cancelarRespuesta"
                class="px-3 py-1 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm"
              >
                Cancelar
              </button>
            </div>
          </div>

          <!-- Respuestas -->
          <div v-if="comentario.replies && comentario.replies.length > 0" class="mt-4 ml-8 space-y-3">
            <div
              v-for="reply in comentario.replies"
              :key="reply.id"
              class="p-3 bg-white rounded-lg border border-gray-200"
            >
              <div class="flex items-start">
                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-2">
                  {{ getInitials(reply.user.name) }}
                </div>
                <div class="flex-1">
                  <div class="flex items-center mb-1">
                    <p class="font-medium text-gray-900 text-sm">{{ reply.user.name }}</p>
                    <p class="text-xs text-gray-500 ml-2">{{ formatDate(reply.created_at) }}</p>
                  </div>
                  <p class="text-gray-700 text-sm">{{ reply.contenido }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Paginación -->
      <div v-if="pagination && pagination.total > pagination.per_page" class="mt-6 flex justify-center">
        <button
          @click="loadMore"
          :disabled="loading || !pagination.next_page_url"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
        >
          {{ loading ? 'Cargando...' : 'Cargar más comentarios' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'ComentariosSection',
  props: {
    incidenciaId: {
      type: Number,
      required: true
    },
    compact: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const authStore = useAuthStore()
    
    const comentarios = ref([])
    const loading = ref(false)
    const showForm = ref(false)
    const nuevoComentario = ref({
      contenido: '',
      incidencia_id: props.incidenciaId,
      parent_id: null
    })
    const replyingTo = ref(null)
    const respuestaContenido = ref('')
    const pagination = ref(null)

    const currentUser = computed(() => authStore.user)

    const loadComentarios = async () => {
      loading.value = true
      try {
        const response = await api.get(`/incidencias/${props.incidenciaId}/comentarios`)
        if (response.data.success) {
          comentarios.value = response.data.data.data || response.data.data
          pagination.value = response.data.data
        }
      } catch (error) {
        console.error('Error cargando comentarios:', error)
      } finally {
        loading.value = false
      }
    }

    const enviarComentario = async () => {
      if (!nuevoComentario.value.contenido.trim()) return
      
      loading.value = true
      try {
        const datosEnvio = {
          contenido: nuevoComentario.value.contenido,
          incidencia_id: props.incidenciaId,
          parent_id: nuevoComentario.value.parent_id || null,
          // Workaround: Laravel está pidiendo estos campos que no debería necesitar
          descripcion: 'comentario', // Campo que Laravel pide innecesariamente
          user_id: authStore.user?.id || null // Agregar el user_id del usuario autenticado
        }
        
        console.log('Datos enviados:', datosEnvio)
        
        const response = await api.post('/comentarios', datosEnvio)
        console.log('Respuesta comentario:', response.data)
        
        if (response.data.success) {
          comentarios.value.unshift(response.data.data)
          nuevoComentario.value.contenido = ''
          showForm.value = false
        } else {
          alert('Error: ' + (response.data.message || 'No se pudo enviar el comentario'))
        }
      } catch (error) {
        console.error('Error enviando comentario:', error)
        console.log('Respuesta completa del error:', error.response?.data)
        console.log('Status del error:', error.response?.status)
        console.log('Headers del error:', error.response?.headers)
        
        let errorMessage = 'Error al enviar el comentario'
        
        if (error.response && error.response.data) {
          console.log('Datos del error 422:', JSON.stringify(error.response.data, null, 2))
          
          if (error.response.status === 422) {
            // Error de validación
            const errors = error.response.data.errors
            if (errors) {
              const errorMessages = Object.values(errors).flat()
              errorMessage = 'Error de validación: ' + errorMessages.join(', ')
            } else if (error.response.data.message) {
              errorMessage = error.response.data.message
            }
          } else if (error.response.data.message) {
            errorMessage = error.response.data.message
          }
          if (error.response.status === 401) {
            errorMessage = 'Debes iniciar sesión para comentar'
          }
        }
        
        alert(errorMessage)
      } finally {
        loading.value = false
      }
    }

    const responderComentario = (comentario) => {
      replyingTo.value = comentario.id
      respuestaContenido.value = ''
    }

    const enviarRespuesta = async (parentComentario) => {
      if (!respuestaContenido.value.trim()) return
      
      loading.value = true
      try {
        const response = await api.post('/comentarios', {
          contenido: respuestaContenido.value,
          incidencia_id: props.incidenciaId,
          parent_id: parentComentario.id
        })
        
        if (response.data.success) {
          // Agregar respuesta al comentario padre
          if (!parentComentario.replies) {
            parentComentario.replies = []
          }
          parentComentario.replies.push(response.data.data)
          
          respuestaContenido.value = ''
          replyingTo.value = null
        }
      } catch (error) {
        console.error('Error enviando respuesta:', error)
        alert('Error al enviar la respuesta')
      } finally {
        loading.value = false
      }
    }

    const editarComentario = (comentario) => {
      const nuevoContenido = prompt('Editar comentario:', comentario.contenido)
      if (nuevoContenido && nuevoContenido.trim()) {
        updateComentario(comentario.id, nuevoContenido)
      }
    }

    const updateComentario = async (id, contenido) => {
      loading.value = true
      try {
        const response = await api.put(`/comentarios/${id}`, { contenido })
        if (response.data.success) {
          const index = comentarios.value.findIndex(c => c.id === id)
          if (index !== -1) {
            comentarios.value[index].contenido = contenido
          }
        }
      } catch (error) {
        console.error('Error actualizando comentario:', error)
        alert('Error al actualizar el comentario')
      } finally {
        loading.value = false
      }
    }

    const eliminarComentario = async (comentario) => {
      if (!confirm('¿Estás seguro de eliminar este comentario?')) return
      
      loading.value = true
      try {
        const response = await api.delete(`/comentarios/${comentario.id}`)
        if (response.data.success) {
          const index = comentarios.value.findIndex(c => c.id === comentario.id)
          if (index !== -1) {
            comentarios.value.splice(index, 1)
          }
        }
      } catch (error) {
        console.error('Error eliminando comentario:', error)
        alert('Error al eliminar el comentario')
      } finally {
        loading.value = false
      }
    }

    const toggleForm = () => {
      showForm.value = !showForm.value
      if (showForm.value) {
        nuevoComentario.value.contenido = ''
      }
    }

    const cancelarForm = () => {
      showForm.value = false
      nuevoComentario.value.contenido = ''
    }

    const cancelarRespuesta = () => {
      replyingTo.value = null
      respuestaContenido.value = ''
    }

    const canEdit = (comentario) => {
      return currentUser.value && comentario.user_id === currentUser.value.id
    }

    const canDelete = (comentario) => {
      return currentUser.value && (
        comentario.user_id === currentUser.value.id || 
        currentUser.value.role === 'admin'
      )
    }

    const getInitials = (name) => {
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      loadComentarios()
    })

    return {
      comentarios,
      loading,
      showForm,
      nuevoComentario,
      replyingTo,
      respuestaContenido,
      pagination,
      currentUser,
      authStore,
      loadComentarios,
      enviarComentario,
      responderComentario,
      enviarRespuesta,
      editarComentario,
      eliminarComentario,
      toggleForm,
      cancelarForm,
      cancelarRespuesta,
      canEdit,
      canDelete,
      getInitials,
      formatDate
    }
  }
}
</script>
