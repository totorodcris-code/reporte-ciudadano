<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <i class="fas fa-chart-line text-blue-600 text-2xl mr-3"></i>
            <h1 class="text-xl font-bold text-gray-900">Dashboard de Analytics</h1>
          </div>
          <div class="flex items-center gap-4">
            <button 
              @click="refreshData"
              :disabled="loading"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center gap-2"
            >
              <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
              Actualizar Datos
            </button>
            <button 
              @click="exportData"
              :disabled="loading"
              class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors flex items-center gap-2"
            >
              <i class="fas fa-download"></i>
              Exportar
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
            <input
              v-model="filters.startDate"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
            <input
              v-model="filters.endDate"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
            <select
              v-model="filters.categoriaId"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Todas las categorías</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                {{ cat.nombre_categoria }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Acciones</label>
            <div class="flex gap-2">
              <button
                @click="applyFilters"
                :disabled="loading"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
              >
                Aplicar Filtros
              </button>
              <button
                @click="clearFilters"
                :disabled="loading"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        <p class="ml-4 text-gray-600">Cargando estadísticas...</p>
      </div>

      <!-- Analytics Content -->
      <div v-else class="space-y-8">
        <!-- Tarjetas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <AnalyticsCard
            title="Total de Reportes"
            subtitle="Todos los reportes"
            :value="analyticsData.totales.total"
            :change="analyticsData.totales.change"
            color="blue"
            :trend="analyticsData.totales.trend"
            :trend-text="analyticsData.totales.trendText"
          />
          
          <AnalyticsCard
            title="Reportes Pendientes"
            subtitle="Esperando atención"
            :value="analyticsData.totales.pendientes"
            :change="analyticsData.totales.pendientesChange"
            color="yellow"
            :trend="analyticsData.totales.pendientesTrend"
            :trend-text="analyticsData.totales.pendientesTrendText"
          />
          
          <AnalyticsCard
            title="Reportes en Progreso"
            subtitle="Siendo atendidos"
            :value="analyticsData.totales.en_progreso"
            :change="analyticsData.totales.enProgresoChange"
            color="purple"
            :trend="analyticsData.totales.enProgresoTrend"
            :trend-text="analyticsData.totales.enProgresoTrendText"
          />
          
          <AnalyticsCard
            title="Reportes Resueltos"
            subtitle="Completados"
            :value="analyticsData.totales.resueltos"
            :change="analyticsData.totales.resueltosChange"
            color="green"
            :trend="analyticsData.totales.resueltosTrend"
            :trend-text="analyticsData.totales.resueltosTrendText"
          />
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          <!-- Gráfico de Barras por Categoría -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reportes por Categoría</h3>
            <BarChart
              title="Distribución por Categoría"
              :data="analyticsData.por_categoria.map(item => item.total)"
              :labels="analyticsData.por_categoria.map(item => item.categoria)"
              color="blue"
            />
          </div>

          <!-- Gráfico de Pastel por Estado -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reportes por Estado</h3>
            <PieChart
              title="Distribución por Estado"
              :data="[
                { label: 'Pendientes', value: analyticsData.totales.pendientes },
                { label: 'En Progreso', value: analyticsData.totales.en_progreso },
                { label: 'Resueltos', value: analyticsData.totales.resueltos }
              ]"
            />
          </div>
        </div>

        <!-- Gráfico de Líneas Temporal -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia Temporal</h3>
          <LineChart
            title="Evolución de Reportes"
            :data="analyticsData.por_mes.map(item => item.total)"
            :labels="analyticsData.por_mes.map(item => formatMonth(item.mes))"
            color="green"
            :period-text="getPeriodText()"
          />
        </div>

        <!-- Estadísticas Adicionales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Tiempo Promedio de Resolución -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tiempo Promedio de Resolución</h3>
            <div class="text-center">
              <div class="text-4xl font-bold text-blue-600">{{ analyticsData.tiempo_promedio_resolucion }}</div>
              <div class="text-gray-600">días promedio</div>
            </div>
          </div>

          <!-- Usuarios Más Activos -->
          <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Usuarios Más Activos</h3>
            <div class="space-y-3">
              <div v-for="(usuario, index) in analyticsData.usuarios_mas_activos" :key="usuario.user_id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                  <div class="font-medium text-gray-900">{{ usuario.nombre }}</div>
                  <div class="text-sm text-gray-600">{{ usuario.email }}</div>
                </div>
                <div class="text-lg font-bold text-blue-600">{{ usuario.total_reportes }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import AnalyticsCard from '../../Components/Charts/AnalyticsCard.vue'
import BarChart from '../../Components/Charts/BarChart.vue'
import PieChart from '../../Components/Charts/PieChart.vue'
import LineChart from '../../Components/Charts/LineChart.vue'

export default {
  name: 'Analytics',
  components: {
    AnalyticsCard,
    BarChart,
    PieChart,
    LineChart
  },
  data() {
    return {
      loading: false,
      analyticsData: {
        totales: {
          total: 0,
          pendientes: 0,
          en_progreso: 0,
          resueltos: 0,
          change: 0,
          trend: 'stable',
          trendText: 'Sin cambios'
        },
        por_categoria: [],
        por_mes: [],
        por_dia: [],
        tiempo_promedio_resolucion: 0,
        usuarios_mas_activos: []
      },
      previousData: null,
      filters: {
        startDate: '',
        endDate: '',
        categoriaId: ''
      },
      categorias: []
    }
  },
  computed: {
    tasaResolucion() {
      const { resueltos, total } = this.analyticsData.totales
      return total > 0 ? Math.round((resueltos / total) * 100) : 0
    }
  },
  methods: {
    async loadAnalytics() {
      this.loading = true
      try {
        const params = new URLSearchParams()
        if (this.filters.startDate) params.append('start_date', this.filters.startDate)
        if (this.filters.endDate) params.append('end_date', this.filters.endDate)
        if (this.filters.categoriaId) params.append('categoria_id', this.filters.categoriaId)

        const response = await api.get(`/analytics?${params}`)
        
        if (response.data.success) {
          this.analyticsData = response.data.data
          this.calculateChanges()
        } else {
          this.analyticsData = response.data
          this.calculateChanges()
        }
      } catch (error) {
        console.error('Error cargando analytics:', error)
        alert('Error al cargar las estadísticas')
      } finally {
        this.loading = false
      }
    },
    
    calculateChanges() {
      if (!this.previousData) {
        this.previousData = { ...this.analyticsData.totales }
        return
      }

      const current = this.analyticsData.totales
      const previous = this.previousData

      // Calcular cambios
      this.analyticsData.totales.change = current.total - previous.total
      this.analyticsData.totales.trend = current.total > previous.total ? 'up' : current.total < previous.total ? 'down' : 'stable'
      this.analyticsData.totales.trendText = this.getTrendText(this.analyticsData.totales.trend)

      this.analyticsData.totales.pendientesChange = current.pendientes - previous.pendientes
      this.analyticsData.totales.pendientesTrend = current.pendientes > previous.pendientes ? 'up' : current.pendientes < previous.pendientes ? 'down' : 'stable'
      this.analyticsData.totales.pendientesTrendText = this.getTrendText(this.analyticsData.totales.pendientesTrend)

      this.analyticsData.totales.enProgresoChange = current.en_progreso - previous.en_progreso
      this.analyticsData.totales.enProgresoTrend = current.en_progreso > previous.en_progreso ? 'up' : current.en_progreso < previous.en_progreso ? 'down' : 'stable'
      this.analyticsData.totales.enProgresoTrendText = this.getTrendText(this.analyticsData.totales.enProgresoTrend)

      this.analyticsData.totales.resueltosChange = current.resueltos - previous.resueltos
      this.analyticsData.totales.resueltosTrend = current.resueltos > previous.resueltos ? 'up' : current.resueltos < previous.resueltos ? 'down' : 'stable'
      this.analyticsData.totales.resueltosTrendText = this.getTrendText(this.analyticsData.totales.resueltosTrend)
    },
    
    getTrendText(trend) {
      const trendTexts = {
        up: 'En aumento',
        down: 'En disminución',
        stable: 'Estable'
      }
      return trendTexts[trend] || trendTexts.stable
    },
    
    async loadCategorias() {
      try {
        const response = await api.get('/categorias')
        this.categorias = response.data.data || response.data
      } catch (error) {
        console.error('Error cargando categorías:', error)
      }
    },
    
    formatMonth(mes) {
      const meses = {
        '01': 'Enero', '02': 'Febrero', '03': 'Marzo', '04': 'Abril',
        '05': 'Mayo', '06': 'Junio', '07': 'Julio', '08': 'Agosto',
        '09': 'Septiembre', '10': 'Octubre', '11': 'Noviembre', '12': 'Diciembre'
      }
      
      const [year, month] = mes.split('-')
      return `${meses[month]} ${year}`
    },
    
    getPeriodText() {
      if (this.filters.startDate || this.filters.endDate) {
        return 'Período personalizado'
      }
      return 'Últimos 30 días'
    },
    
    async refreshData() {
      await this.loadAnalytics()
    },
    
    async applyFilters() {
      await this.loadAnalytics()
    },
    
    clearFilters() {
      this.filters = {
        startDate: '',
        endDate: '',
        categoriaId: ''
      }
      this.loadAnalytics()
    },
    
    async exportData() {
      try {
        const params = new URLSearchParams()
        if (this.filters.startDate) params.append('start_date', this.filters.startDate)
        if (this.filters.endDate) params.append('end_date', this.filters.endDate)
        if (this.filters.categoriaId) params.append('categoria_id', this.filters.categoriaId)

        const response = await api.get(`/analytics?${params}`)
        const data = response.data.success ? response.data.data : response.data
        
        // Crear CSV
        let csv = 'Categoría,Total,Pendientes,En Progreso,Resueltos,Tasa Resolución\n'
        
        data.por_categoria.forEach(item => {
          csv += `"${item.categoria}",${item.total},${item.resueltos || 0},${item.en_progreso || 0},${item.pendientes || 0},${((item.resueltos || 0) / item.total * 100).toFixed(1)}%\n`
        })
        
        // Descargar archivo
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `analytics_${new Date().toISOString().split('T')[0]}.csv`
        link.click()
        window.URL.revokeObjectURL(url)
        
      } catch (error) {
        console.error('Error exportando datos:', error)
        alert('Error al exportar los datos')
      }
    }
  },
  
  async mounted() {
    await this.loadCategorias()
    await this.loadAnalytics()
  }
}
</script>
