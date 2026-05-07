<template>
  <div class="space-y-6">
    <!-- Analytics Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
      <AnalyticsCard
        title="Total de Incidencias"
        :value="analytics.totalIncidencias"
        :change="analytics.totalIncidenciasChange"
        changePercent="+12.5%"
        :subtitle="vs. mes anterior"
        color="blue"
        trend="up"
        trendText="Tendencia positiva"
      />
      
      <AnalyticsCard
        title="Resueltas Hoy"
        :value="analytics.resueltasHoy"
        :change="analytics.resueltasHoyChange"
        changePercent="+8.3%"
        :subtitle="vs. ayer"
        color="green"
        trend="up"
        trendText="Mejor rendimiento"
      />
      
      <AnalyticsCard
        title="Tasa de Respuesta"
        :value="analytics.tasaRespuesta"
        :change="analytics.tasaRespuestaChange"
        changePercent="-2.1%"
        :subtitle="vs. semana pasada"
        color="yellow"
        trend="down"
        trendText="Ligera disminución"
      />
      
      <AnalyticsCard
        title="Usuarios Activos"
        :value="analytics.usuariosActivos"
        :change="analytics.usuariosActivosChange"
        changePercent="+15.2%"
        :subtitle="vs. mes anterior"
        color="purple"
        trend="up"
        trendText="Crecimiento constante"
      />
    </div>
    
    <!-- Detailed Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Incidencias por Categoría -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Incidencias por Categoría</h3>
        <div class="h-64">
          <canvas ref="categoryChart"></canvas>
        </div>
      </div>
      
      <!-- Tendencia Temporal -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia de Incidencias</h3>
        <div class="h-64">
          <canvas ref="trendChart"></canvas>
        </div>
      </div>
    </div>
    
    <!-- Mapa de Calor -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Mapa de Calor de Incidencias</h3>
      <div class="h-96">
        <canvas ref="heatmapChart"></canvas>
      </div>
    </div>
    
    <!-- Insights and Recommendations -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Key Insights -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Insights Clave</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
              <i class="fas fa-arrow-up text-green-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Aumento del 25% en reportes de tráfico</p>
              <p class="text-xs text-gray-600">Comparado con el mes anterior</p>
            </div>
          </div>
          
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Tiempo de respuesta promedio: 2.5 horas</p>
              <p class="text-xs text-gray-600">Por encima del objetivo de 2 horas</p>
            </div>
          </div>
          
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
              <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900">Participación del 78% de usuarios activos</p>
              <p class="text-xs text-gray-600">Mejor que el promedio del 65%</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Recommendations -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recomendaciones</h3>
        <div class="space-y-4">
          <div class="border-l-4 border-blue-500 pl-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-1">Optimizar Tiempo de Respuesta</h4>
            <p class="text-sm text-gray-600">Asignar más personal a las categorías con mayor volumen de incidencias para reducir el tiempo de respuesta actual de 2.5 horas a 2 horas.</p>
          </div>
          
          <div class="border-l-4 border-green-500 pl-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-1">Fomentar Reportes Tempranos</h4>
            <p class="text-sm text-gray-600">Implementar un programa de incentivos para reportes tempranos que podría reducir el tiempo de resolución en un 30%.</p>
          </div>
          
          <div class="border-l-4 border-yellow-500 pl-4">
            <h4 class="text-sm font-semibold text-gray-900 mb-1">Capacitar al Personal</h4>
            <p class="text-sm text-gray-600">Realizar talleres mensuales sobre las categorías más reportadas para mejorar la calidad de las respuestas.</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Export Options -->
    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Exportar Informes</h3>
        <DataExport
          :data="analyticsData"
          :available-columns="exportColumns"
          export-endpoint="/api/analytics/export"
        @export-complete="handleExportComplete"
        @export-error="handleExportError"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { Chart, registerables } from 'chart.js'
import AnalyticsCard from '@/Components/Charts/AnalyticsCard.vue'
import DataExport from '@/Components/DataExport.vue'

export default {
  name: 'AnalyticsDashboard',
  components: {
    AnalyticsCard,
    DataExport
  },
  props: {
    timeRange: {
      type: String,
      default: '30d'
    }
  },
  setup(props) {
    const categoryChart = ref(null)
    const trendChart = ref(null)
    const heatmapChart = ref(null)
    
    const analytics = ref({
      totalIncidencias: 1247,
      totalIncidenciasChange: 156,
      resueltasHoy: 89,
      resueltasHoyChange: 7,
      tasaRespuesta: '2.5h',
      tasaRespuestaChange: '-0.1h',
      usuariosActivos: 342,
      usuariosActivosChange: 45
    })
    
    const analyticsData = ref([])
    const exportColumns = [
      { key: 'id', label: 'ID' },
      { key: 'titulo', label: 'Título' },
      { key: 'categoria', label: 'Categoría' },
      { key: 'estado', label: 'Estado' },
      { key: 'fecha_creacion', label: 'Fecha Creación' },
      { key: 'fecha_resolucion', label: 'Fecha Resolución' },
      { key: 'tiempo_respuesta', label: 'Tiempo Respuesta' },
      { key: 'usuario_asignado', label: 'Usuario Asignado' }
    ]
    
    let categoryChartInstance = null
    let trendChartInstance = null
    let heatmapChartInstance = null
    
    const initCategoryChart = () => {
      if (!categoryChart.value) return
      
      const ctx = categoryChart.value.getContext('2d')
      categoryChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Tráfico', 'Alumbrado', 'Basura', 'Seguridad', 'Vialidad', 'Otro'],
          datasets: [{
            data: [342, 278, 195, 156, 134, 142],
            backgroundColor: [
              'rgb(59, 130, 246)',
              'rgb(251, 146, 60)',
              'rgb(156, 163, 175)',
              'rgb(239, 68, 68)',
              'rgb(34, 197, 94)',
              'rgb(168, 85, 247)'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      })
    }
    
    const initTrendChart = () => {
      if (!trendChart.value) return
      
      const ctx = trendChart.value.getContext('2d')
      trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
          datasets: [{
            label: 'Incidencias',
            data: [89, 95, 123, 145, 167, 189],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      })
    }
    
    const initHeatmapChart = () => {
      if (!heatmapChart.value) return
      
      // Simulate heatmap data
      const ctx = heatmapChart.value.getContext('2d')
      const gradient = ctx.createLinearGradient(0, 0, 0, 100)
      gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)')
      gradient.addColorStop(0.5, 'rgba(251, 146, 60, 0.6)')
      gradient.addColorStop(1, 'rgba(156, 163, 175, 0.4)')
      
      // Create simple heatmap visualization
      ctx.fillStyle = gradient
      ctx.fillRect(0, 0, ctx.canvas.width, ctx.canvas.height)
      
      // Add some "hot spots"
      ctx.fillStyle = 'rgba(239, 68, 68, 0.6)'
      for (let i = 0; i < 8; i++) {
        const x = Math.random() * ctx.canvas.width
        const y = Math.random() * ctx.canvas.height
        const radius = Math.random() * 30 + 10
        ctx.beginPath()
        ctx.arc(x, y, radius, 0, 0, 2 * Math.PI)
        ctx.fill()
      }
    }
    
    const handleExportComplete = (data) => {
      console.log('Analytics export completed:', data)
      // Show success message
    }
    
    const handleExportError = (error) => {
      console.error('Analytics export error:', error)
      // Show error message
    }
    
    onMounted(() => {
      // Initialize charts after DOM is ready
      setTimeout(() => {
        initCategoryChart()
        initTrendChart()
        initHeatmapChart()
      }, 100)
    })
    
    onUnmounted(() => {
      // Cleanup chart instances
      if (categoryChartInstance) categoryChartInstance.destroy()
      if (trendChartInstance) trendChartInstance.destroy()
      if (heatmapChartInstance) {
        // Cleanup heatmap if needed
      }
    })
    
    return {
      analytics,
      analyticsData,
      exportColumns,
      categoryChart,
      trendChart,
      heatmapChart,
      handleExportComplete,
      handleExportError
    }
  }
}
</script>
