<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
          {{ isAdmin ? 'Panel de Votos (Administrador)' : 'Mis Votos' }}
        </h1>
        <p class="text-gray-600">
          {{ isAdmin ? 'Visualiza todos los votos del sistema' : 'Visualiza los votos de tus incidencias' }}
        </p>
      </div>

      <!-- Estadísticas Generales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-vote-yea text-white text-xl"></i>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Votos</p>
              <p class="text-2xl font-bold text-gray-900">{{ estadisticas.total_votos }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-thumbs-up text-white text-xl"></i>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Votos Positivos</p>
              <p class="text-2xl font-bold text-green-600">{{ estadisticas.votos_positivos }}</p>
              <p class="text-xs text-gray-500">{{ estadisticas.porcentaje_positivos }}%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-thumbs-down text-white text-xl"></i>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Votos Negativos</p>
              <p class="text-2xl font-bold text-red-600">{{ estadisticas.votos_negativos }}</p>
              <p class="text-xs text-gray-500">{{ estadisticas.porcentaje_negativos }}%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Incidencias con Votos</p>
              <p class="text-2xl font-bold text-purple-600">{{ estadisticas.incidencias_con_votos }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtros (solo para administrador) -->
      <div v-if="isAdmin" class="bg-white rounded-lg shadow p-6 mb-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
          <i class="fas fa-filter text-blue-600 mr-2"></i>
          Filtros
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

      <!-- Lista de Votos -->
      <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-list text-blue-600 mr-2"></i>
            Lista de Votos
          </h3>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mr-3"></div>
          <span class="text-gray-600">Cargando votos...</span>
        </div>

        <!-- Tabla de Votos -->
        <div v-else-if="votos.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Usuario
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Incidencia
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Fecha
                </th>
                <th v-if="isAdmin" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="voto in votos" :key="voto.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                        {{ getInitials(voto.user?.name) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ voto.user?.name }}</div>
                      <div class="text-sm text-gray-500">{{ voto.user?.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ voto.incidencia?.titulo || 'Incidencia #' + voto.incidencia_id }}</div>
                  <div class="text-sm text-gray-500">ID: {{ voto.incidencia_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(voto.created_at) }}
                </td>
                <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <button @click="verDetallesVoto(voto)" 
                          class="text-blue-600 hover:text-blue-900 mr-3">
                    <i class="fas fa-eye"></i>
                  </button>
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
        <div v-else class="text-center py-12">
          <i class="fas fa-vote-yea text-gray-400 text-5xl mb-4"></i>
          <p class="text-gray-500 text-lg">No se encontraron votos</p>
          <p class="text-gray-400 text-sm">Intenta con otros filtros</p>
        </div>

        <!-- Paginación -->
        <div v-if="pagination.total > pagination.per_page" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button @click="paginaAnterior" :disabled="pagination.current_page === 1"
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Anterior
              </button>
              <button @click="paginaSiguiente" :disabled="pagination.current_page === pagination.last_page"
                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Siguiente
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Mostrando <span class="font-medium">{{ pagination.from }}</span> a 
                  <span class="font-medium">{{ pagination.to }}</span> de 
                  <span class="font-medium">{{ pagination.total }}</span> resultados
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                  <button @click="paginaAnterior" :disabled="pagination.current_page === 1"
                          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                    Página {{ pagination.current_page }} de {{ pagination.last_page }}
                  </span>
                  <button @click="paginaSiguiente" :disabled="pagination.current_page === pagination.last_page"
                          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </nav>
              </div>
            </div>
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
  name: 'VotosDashboard',
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

    const isAdmin = computed(() => {
      return authStore.user?.role === 'admin'
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

    const verDetallesVoto = (voto) => {
      // Implementar modal de detalles
      console.log('Ver detalles del voto:', voto)
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
      isAdmin,
      cargarVotos,
      aplicarFiltros,
      paginaAnterior,
      paginaSiguiente,
      getInitials,
      formatDate,
      verDetallesVoto,
      eliminarVoto
    }
  }
}
</script>
