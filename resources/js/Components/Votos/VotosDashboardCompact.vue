<template>
  <div class="space-y-6">
    <!-- Estadísticas de Votos -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
              <i class="fas fa-vote-yea text-white"></i>
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Total Votos</p>
            <p class="text-xl font-bold text-gray-900">{{ estadisticas.total_votos }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
              <i class="fas fa-thumbs-up text-white"></i>
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Positivos</p>
            <p class="text-xl font-bold text-green-600">{{ estadisticas.votos_positivos }}</p>
            <p class="text-xs text-gray-500">{{ estadisticas.porcentaje_positivos }}%</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
              <i class="fas fa-thumbs-down text-white"></i>
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Negativos</p>
            <p class="text-xl font-bold text-red-600">{{ estadisticas.votos_negativos }}</p>
            <p class="text-xs text-gray-500">{{ estadisticas.porcentaje_negativos }}%</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
              <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-600">Incidencias con Votos</p>
            <p class="text-xl font-bold text-purple-600">{{ estadisticas.incidencias_con_votos }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros (solo para admin) -->
    <div v-if="isAdmin" class="bg-white rounded-lg shadow p-4 border border-gray-200">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          <i class="fas fa-filter text-blue-600 mr-2"></i>
          Filtros
        </h3>
        <button 
          @click="toggleFiltros" 
          class="text-gray-500 hover:text-gray-700 transition-colors"
        >
          <i :class="mostrarFiltros ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
        </button>
      </div>
      
      <div v-show="mostrarFiltros" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Voto</label>
          <select v-model="filtros.tipo" @change="aplicarFiltros" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">Todos</option>
            <option value="positivo">Positivos</option>
            <option value="negativo">Negativos</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
          <input type="date" v-model="filtros.fecha_inicio" @change="aplicarFiltros"
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
          <input type="date" v-model="filtros.fecha_fin" @change="aplicarFiltros"
                 class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
      </div>
    </div>

    <!-- Lista de Votos Recientes -->
    <div class="bg-white rounded-lg shadow border border-gray-200">
      <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">
          <i class="fas fa-list text-blue-600 mr-2"></i>
          Votos Recientes
        </h3>
        <button 
          @click="toggleListaCompleta" 
          class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors"
        >
          {{ mostrarListaCompleta ? 'Ver Menos' : 'Ver Todos' }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mr-2"></div>
        <span class="text-gray-600">Cargando votos...</span>
      </div>

      <!-- Tabla de Votos -->
      <div v-else-if="votos.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Usuario
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Incidencia
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tipo
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Fecha
              </th>
              <th v-if="isAdmin" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(voto, index) in votosMostrados" :key="voto.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold">
                      {{ getInitials(voto.user?.name) }}
                    </div>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ voto.user?.name }}</div>
                    <div class="text-xs text-gray-500">{{ voto.user?.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="text-sm text-gray-900">{{ voto.incidencia?.titulo || 'Incidencia #' + voto.incidencia_id }}</div>
                <div class="text-xs text-gray-500">ID: {{ voto.incidencia_id }}</div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span :class="[
                  'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                  voto.tipo === 'positivo' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                ]">
                  <i :class="[
                    'fas mr-1',
                    voto.tipo === 'positivo' ? 'fa-thumbs-up' : 'fa-thumbs-down'
                  ]"></i>
                  {{ voto.tipo === 'positivo' ? 'Positivo' : 'Negativo' }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(voto.created_at) }}
              </td>
              <td v-if="isAdmin" class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                <button @click="eliminarVoto(voto)" 
                        class="text-red-600 hover:text-red-900">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Sin resultados -->
      <div v-else class="text-center py-8">
        <i class="fas fa-vote-yea text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-500">No se encontraron votos</p>
        <p class="text-gray-400 text-sm">Intenta con otros filtros</p>
      </div>

      <!-- Botón Ver Más -->
      <div v-if="!mostrarListaCompleta && votos.length > 5" class="px-4 py-3 border-t border-gray-200 text-center">
        <button 
          @click="toggleListaCompleta" 
          class="text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors"
        >
          Ver todos los votos ({{ votos.length }})
        </button>
      </div>

      <!-- Paginación (solo cuando se muestra lista completa) -->
      <div v-if="mostrarListaCompleta && pagination.total > pagination.per_page" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Mostrando <span class="font-medium">{{ pagination.from }}</span> a 
            <span class="font-medium">{{ pagination.to }}</span> de 
            <span class="font-medium">{{ pagination.total }}</span> resultados
          </div>
          <div class="flex space-x-2">
            <button @click="paginaAnterior" :disabled="pagination.current_page === 1"
                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-chevron-left"></i>
            </button>
            <span class="px-3 py-1 text-sm text-gray-700">
              Página {{ pagination.current_page }} de {{ pagination.last_page }}
            </span>
            <button @click="paginaSiguiente" :disabled="pagination.current_page === pagination.last_page"
                    class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

export default {
  name: 'VotosDashboardCompact',
  props: {
    compact: {
      type: Boolean,
      default: false
    }
  },
  setup() {
    const authStore = useAuthStore()
    
    const votos = ref([])
    const estadisticas = ref({
      total_votos: 0,
      votos_positivos: 0,
      votos_negativos: 0,
      incidencias_con_votos: 0,
      porcentaje_positivos: 0,
      porcentaje_negativos: 0,
      votos_por_dia: []
    })
    
    const loading = ref(false)
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 50,
      total: 0,
      from: 0,
      to: 0
    })
    
    const filtros = ref({
      tipo: '',
      fecha_inicio: '',
      fecha_fin: ''
    })

    const mostrarFiltros = ref(false)
    const mostrarListaCompleta = ref(false)

    const isAdmin = computed(() => {
      return authStore.user?.role === 'admin'
    })

    const votosMostrados = computed(() => {
      if (mostrarListaCompleta.value) {
        return votos.value
      }
      return votos.value.slice(0, 5)
    })

    const cargarVotos = async (pagina = 1) => {
      loading.value = true
      try {
        const params = {
          page: pagina,
          ...filtros.value
        }
        
        // Eliminar filtros vacíos
        Object.keys(params).forEach(key => {
          if (params[key] === '') {
            delete params[key]
          }
        })

        const response = await api.get('/votos', { params })
        
        if (response.data.success) {
          votos.value = response.data.data
          pagination.value = response.data.pagination
        }
      } catch (error) {
        console.error('Error cargando votos:', error)
      } finally {
        loading.value = false
      }
    }

    const cargarEstadisticas = async () => {
      try {
        const response = await api.get('/votos/estadisticas')
        
        if (response.data.success) {
          estadisticas.value = response.data.data
        }
      } catch (error) {
        console.error('Error cargando estadísticas:', error)
      }
    }

    const aplicarFiltros = () => {
      cargarVotos(1)
    }

    const toggleFiltros = () => {
      mostrarFiltros.value = !mostrarFiltros.value
    }

    const toggleListaCompleta = () => {
      mostrarListaCompleta.value = !mostrarListaCompleta.value
    }

    const paginaAnterior = () => {
      if (pagination.value.current_page > 1) {
        cargarVotos(pagination.value.current_page - 1)
      }
    }

    const paginaSiguiente = () => {
      if (pagination.value.current_page < pagination.value.last_page) {
        cargarVotos(pagination.value.current_page + 1)
      }
    }

    const getInitials = (name) => {
      if (!name) return 'NA'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const eliminarVoto = async (voto) => {
      if (!confirm('¿Estás seguro de que quieres eliminar este voto?')) {
        return
      }

      try {
        await api.delete(`/votos/${voto.id}`)
        await cargarVotos(pagination.value.current_page)
        await cargarEstadisticas()
      } catch (error) {
        console.error('Error eliminando voto:', error)
      }
    }

    onMounted(() => {
      cargarVotos()
      cargarEstadisticas()
    })

    return {
      votos,
      estadisticas,
      loading,
      pagination,
      filtros,
      mostrarFiltros,
      mostrarListaCompleta,
      isAdmin,
      votosMostrados,
      cargarVotos,
      aplicarFiltros,
      toggleFiltros,
      toggleListaCompleta,
      paginaAnterior,
      paginaSiguiente,
      getInitials,
      formatDate,
      eliminarVoto
    }
  }
}
</script>
