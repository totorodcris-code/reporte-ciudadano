<template>
  <div :class="compact ? 'inline-flex items-center gap-2' : 'bg-white rounded-xl shadow-lg p-6 border border-gray-200'">
    <!-- Modo compacto -->
    <div v-if="compact" class="flex items-center gap-1">
      <!-- Usuario logueado, no es su propia incidencia -->
      <template v-if="authStore.isLoggedIn && !esPropiaIncidencia">
        <button
          v-if="votos.user_voto !== 'positivo'"
          @click="votar('positivo')"
          :disabled="loading"
          class="flex items-center gap-1 px-2 py-1 rounded text-xs font-medium transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700"
        >
          <i class="fas fa-thumbs-up"></i>
          <span>{{ votos.positivos }}</span>
        </button>
        <template v-else>
          <button
            disabled
            class="flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-600 text-white cursor-default"
          >
            <i class="fas fa-thumbs-up"></i>
            <span>{{ votos.positivos }}</span>
          </button>
          <button
            @click="eliminarVoto"
            :disabled="loading"
            class="flex items-center gap-1 px-2 py-1 rounded text-xs font-medium transition-all duration-200 bg-red-50 text-red-600 hover:bg-red-100"
          >
            <i class="fas fa-trash-alt"></i>
            <span class="hidden sm:inline">Eliminar</span>
          </button>
        </template>
      </template>
      
      <!-- No autenticado -->
      <button
        v-else-if="!authStore.isLoggedIn"
        @click="$router.push('/login')"
        class="flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors"
      >
        <i class="fas fa-thumbs-up"></i>
        <span>{{ votos.positivos }}</span>
      </button>
      
      <!-- Mensaje para propia incidencia -->
      <div v-else class="text-xs text-gray-500 italic">
        <i class="fas fa-info-circle mr-1"></i>
        No puedes votar en tu reporte
      </div>
    </div>

    <!-- Modo completo -->
    <div v-else>
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          <i class="fas fa-thumbs-up text-blue-600 mr-2"></i>
          Votación
        </h3>
        <div class="text-sm text-gray-600">
          {{ votos.total }} votos totales
        </div>
      </div>

      <!-- Estadísticas de votación -->
      <div class="mb-6">
        <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
          <div class="text-3xl font-bold text-green-600">{{ votos.positivos }}</div>
          <div class="text-sm text-green-700">Positivos</div>
          <div class="text-xs text-green-600 mt-1">
            {{ getPorcentaje(votos.positivos) }}%
          </div>
        </div>
      </div>

      <!-- Barra de progreso visual -->
      <div class="mb-6">
        <div class="relative h-8 bg-gray-200 rounded-full overflow-hidden">
          <div
            class="absolute left-0 top-0 h-full bg-green-500 transition-all duration-300"
            :style="{ width: getPorcentaje(votos.positivos) + '%' }"
          ></div>
        </div>
        <div class="text-xs text-gray-600 mt-1 text-right">
          {{ getPorcentaje(votos.positivos) }}% positivos
        </div>
      </div>

      <!-- Botones de votación -->
      <div v-if="authStore.isLoggedIn && !esPropiaIncidencia" class="flex gap-4">
        <button
          v-if="votos.user_voto !== 'positivo'"
          @click="votar('positivo')"
          :disabled="loading"
          class="flex-1 py-3 px-4 rounded-lg font-medium transition-all duration-200 bg-gray-100 text-gray-700 hover:bg-green-100 hover:text-green-700"
        >
          <i class="fas fa-thumbs-up mr-2"></i>
          Votar Positivo
        </button>
        <div v-else class="flex gap-2 flex-1">
          <button
            disabled
            class="flex-1 py-3 px-4 rounded-lg font-medium bg-green-600 text-white shadow-lg cursor-default"
          >
            <i class="fas fa-thumbs-up mr-2"></i>
            Votado
          </button>
          <button
            @click="eliminarVoto"
            :disabled="loading"
            class="px-4 py-3 rounded-lg font-medium transition-all duration-200 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200"
          >
            <i class="fas fa-trash-alt mr-2"></i>
            Eliminar voto
          </button>
        </div>
      </div>

      <!-- Mensaje para usuarios no autenticados -->
      <div v-else-if="!authStore.isLoggedIn" class="text-center py-4 bg-gray-50 rounded-lg">
        <p class="text-gray-600 mb-3">
          <i class="fas fa-sign-in-alt mr-2"></i>
          Debes iniciar sesión para votar
        </p>
        <button
          @click="$router.push('/login')"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          Iniciar Sesión
        </button>
      </div>

      <!-- Mensaje para propia incidencia -->
      <div v-else class="text-center py-4 bg-yellow-50 rounded-lg border border-yellow-200">
        <p class="text-yellow-700">
          <i class="fas fa-info-circle mr-2"></i>
          No puedes votar en tu propia incidencia
        </p>
      </div>

      <!-- Lista de votantes recientes -->
      <div v-if="votantesRecientes.length > 0" class="mt-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Votantes Recientes</h4>
        <div class="space-y-2">
          <div
            v-for="votante in votantesRecientes"
            :key="votante.id"
            class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
          >
            <div class="flex items-center">
              <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold mr-2">
                {{ getInitials(votante.user.name) }}
              </div>
              <span class="text-sm text-gray-700">{{ votante.user.name }}</span>
            </div>
            <div class="flex items-center">
              <i class="fas fa-thumbs-up text-green-600 text-sm"></i>
              <span class="text-xs text-gray-500 ml-2">{{ formatDate(votante.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Indicador de carga -->
    <div v-if="loading" class="flex items-center justify-center py-4">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mr-2"></div>
      <span class="text-gray-600">Procesando voto...</span>
    </div>

  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'VotacionSection',
  props: {
    incidenciaId: {
      type: Number,
      required: true
    },
    incidenciaUserId: {
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
    
    const votos = ref({
      positivos: 0,
      negativos: 0,
      total: 0,
      user_voto: null
    })
    
    const loading = ref(false)
    const votantesRecientes = ref([])
    const userVotoId = ref(null)

    const esPropiaIncidencia = computed(() => {
      const userId = authStore.user?.id
      const incidenciaUserId = props.incidenciaUserId
      
      return userId && userId === incidenciaUserId
    })

    const loadVotos = async () => {
      try {
        const response = await api.get(`/incidencias/${props.incidenciaId}/votos`)
        if (response.data.success) {
          votos.value = response.data.data
          const miVoto = response.data.data.votos?.find(v => v.user_id === authStore.user?.id)
          userVotoId.value = miVoto?.id || null
        }
      } catch (error) {
        console.error('Error cargando votos:', error)
      }
    }

    const votar = async (tipo) => {
      if (!authStore.isLoggedIn) {
        alert('Debes iniciar sesión para votar')
        return
      }
      
      if (esPropiaIncidencia.value) {
        alert('No puedes votar en tu propia incidencia')
        return
      }
      
      loading.value = true
      try {
        const response = await api.post('/votos', {
          incidencia_id: props.incidenciaId,
          tipo: tipo
        })
        
        if (response.data.success) {
          // Recargar los votos para obtener el estado actualizado
          await loadVotos()
        } else {
          alert('Error: ' + (response.data.message || 'No se pudo procesar tu voto'))
        }
      } catch (error) {
        console.error('Error al votar:', error)
        let errorMessage = 'Error al procesar tu voto'
        
        if (error.response && error.response.data) {
          if (error.response.data.message) {
            errorMessage = error.response.data.message
          }
          if (error.response.status === 401) {
            errorMessage = 'Debes iniciar sesión para votar'
          }
        }
        
        alert(errorMessage)
      } finally {
        loading.value = false
      }
    }

    const eliminarVoto = async () => {
      if (!userVotoId.value) return
      loading.value = true
      try {
        await api.delete(`/votos/${userVotoId.value}`)
        await loadVotos()
      } catch (error) {
        console.error('Error al eliminar voto:', error)
      } finally {
        loading.value = false
      }
    }

    const getPorcentaje = (valor) => {
      if (votos.value.total === 0) return 0
      return Math.round((valor / votos.value.total) * 100)
    }

    const getInitials = (name) => {
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('es-ES', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      loadVotos()
    })

    return {
      votos,
      loading,
      votantesRecientes,
      authStore,
      esPropiaIncidencia,
      userVotoId,
      loadVotos,
      votar,
      eliminarVoto,
      getPorcentaje,
      getInitials,
      formatDate
    }
  }
}
</script>
