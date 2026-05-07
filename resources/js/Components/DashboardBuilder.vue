<template>
  <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Constructor de Dashboard</h3>
      <div class="flex gap-2">
        <button
          @click="previewDashboard"
          class="px-4 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200"
        >
          <i class="fas fa-eye mr-2"></i>
          Vista Previa
        </button>
        <button
          @click="saveDashboard"
          class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200"
        >
          <i class="fas fa-save mr-2"></i>
          Guardar
        </button>
      </div>
    </div>
    
    <!-- Available Components -->
    <div class="mb-6">
      <h4 class="text-md font-medium text-gray-800 mb-3">Componentes Disponibles</h4>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div
          v-for="component in availableComponents"
          :key="component.id"
          draggable="true"
          @dragstart="onDragStart($event, component)"
          class="p-4 bg-gray-50 border border-gray-200 rounded-lg cursor-move hover:bg-gray-100 transition-colors duration-200"
        >
          <i :class="component.icon + ' text-2xl text-gray-600 mb-2'"></i>
          <p class="text-sm font-medium text-gray-900">{{ component.name }}</p>
          <p class="text-xs text-gray-500">{{ component.description }}</p>
        </div>
      </div>
    </div>
    
    <!-- Dashboard Layout -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <h4 class="text-md font-medium text-gray-800">Layout del Dashboard</h4>
        <div class="flex gap-2">
          <button
            @click="clearLayout"
            class="px-3 py-1 text-sm text-red-600 hover:text-red-700 border border-red-300 rounded-lg transition-colors duration-200"
          >
            Limpiar
          </button>
          <select
            v-model="selectedLayout"
            class="px-3 py-1 text-sm border border-gray-300 rounded-lg"
          >
            <option value="grid">Grid</option>
            <option value="flex">Flex</option>
            <option value="custom">Personalizado</option>
          </select>
        </div>
      </div>
      
      <div
        class="min-h-96 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-4"
        :class="getLayoutClass()"
        @dragover.prevent
        @drop="onDrop($event)"
      >
        <div v-if="dashboardComponents.length === 0" class="text-center py-16">
          <i class="fas fa-layer-group text-4xl text-gray-400 mb-4"></i>
          <p class="text-gray-500">Arrastra componentes aquí para construir tu dashboard</p>
        </div>
        
        <div
          v-else
          class="grid gap-4"
          :class="getGridClass()"
        >
          <div
            v-for="(component, index) in dashboardComponents"
            :key="component.id"
            class="relative bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
            :class="getComponentSize(component.size)"
          >
            <!-- Component Header -->
            <div class="flex items-center justify-between p-3 border-b border-gray-100">
              <div class="flex items-center gap-2">
                <i :class="component.icon + ' text-gray-600'"></i>
                <span class="text-sm font-medium text-gray-900">{{ component.name }}</span>
              </div>
              <div class="flex gap-1">
                <button
                  @click="editComponent(component)"
                  class="p-1 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                  <i class="fas fa-edit text-xs"></i>
                </button>
                <button
                  @click="removeComponent(index)"
                  class="p-1 text-gray-400 hover:text-red-600 transition-colors duration-200"
                >
                  <i class="fas fa-times text-xs"></i>
                </button>
              </div>
            </div>
            
            <!-- Component Content -->
            <div class="p-4">
              <component
                :is="getComponentType(component.type)"
                v-bind="component.props"
                :is-editing="true"
              />
            </div>
            
            <!-- Resize Handle -->
            <div class="absolute bottom-2 right-2 flex gap-1">
              <button
                @click="resizeComponent(component, 'small')"
                class="p-1 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                :class="{ 'text-blue-600': component.size === 'small' }"
              >
                <i class="fas fa-compress text-xs"></i>
              </button>
              <button
                @click="resizeComponent(component, 'medium')"
                class="p-1 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                :class="{ 'text-blue-600': component.size === 'medium' }"
              >
                <i class="fas fa-expand text-xs"></i>
              </button>
              <button
                @click="resizeComponent(component, 'large')"
                class="p-1 text-gray-400 hover:text-gray-600 transition-colors duration-200"
                :class="{ 'text-blue-600': component.size === 'large' }"
              >
                <i class="fas fa-expand-arrows-alt text-xs"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Component Settings Modal -->
    <Modal
      v-if="showSettingsModal"
      @close="showSettingsModal = false"
      title="Configurar Componente"
    >
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
          <input
            v-model="editingComponent.title"
            type="text"
            class="input-field"
            placeholder="Título del componente"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Gráfico</label>
          <select v-model="editingComponent.chartType" class="input-field">
            <option value="line">Línea</option>
            <option value="bar">Barra</option>
            <option value="pie">Pastel</option>
            <option value="doughnut">Donut</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Fuente de Datos</label>
          <select v-model="editingComponent.dataSource" class="input-field">
            <option value="incidencias">Incidencias</option>
            <option value="usuarios">Usuarios</option>
            <option value="categorias">Categorías</option>
            <option value="custom">Personalizado</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Intervalo de Actualización</label>
          <select v-model="editingComponent.refreshInterval" class="input-field">
            <option value="realtime">Tiempo Real</option>
            <option value="30s">30 segundos</option>
            <option value="1m">1 minuto</option>
            <option value="5m">5 minutos</option>
            <option value="manual">Manual</option>
          </select>
        </div>
      </div>
      
      <template #actions>
        <button
          @click="showSettingsModal = false"
          class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg transition-colors duration-200"
        >
          Cancelar
        </button>
        <button
          @click="saveComponentSettings"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
        >
          Guardar Configuración
        </button>
      </template>
    </Modal>
    
    <!-- Preview Modal -->
    <Modal
      v-if="showPreviewModal"
      @close="showPreviewModal = false"
      title="Vista Previa del Dashboard"
      size="large"
    >
      <div class="bg-gray-50 rounded-lg p-6">
        <div class="grid gap-4" :class="getGridClass()">
          <div
            v-for="component in dashboardComponents"
            :key="component.id"
            class="bg-white border border-gray-200 rounded-lg shadow-sm"
            :class="getComponentSize(component.size)"
          >
            <component
              :is="getComponentType(component.type)"
              v-bind="component.props"
            />
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import AnalyticsCard from '@/Components/Charts/AnalyticsCard.vue'
import BarChart from '@/Components/Charts/BarChart.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
import PieChart from '@/Components/Charts/PieChart.vue'
import Modal from '@/Components/Modal.vue'

export default {
  name: 'DashboardBuilder',
  components: {
    AnalyticsCard,
    BarChart,
    LineChart,
    PieChart,
    Modal
  },
  setup() {
    const dashboardComponents = ref([])
    const selectedLayout = ref('grid')
    const showSettingsModal = ref(false)
    const showPreviewModal = ref(false)
    const editingComponent = ref({})
    const draggedComponent = ref(null)
    
    const availableComponents = [
      {
        id: 'analytics-card',
        name: 'Tarjeta Analítica',
        description: 'Muestra métricas clave',
        icon: 'fas fa-chart-line',
        type: 'AnalyticsCard'
      },
      {
        id: 'bar-chart',
        name: 'Gráfico de Barras',
        description: 'Comparación de datos',
        icon: 'fas fa-chart-bar',
        type: 'BarChart'
      },
      {
        id: 'line-chart',
        name: 'Gráfico de Líneas',
        description: 'Tendencias temporales',
        icon: 'fas fa-chart-line',
        type: 'LineChart'
      },
      {
        id: 'pie-chart',
        name: 'Gráfico Circular',
        description: 'Distribución porcentual',
        icon: 'fas fa-chart-pie',
        type: 'PieChart'
      },
      {
        id: 'data-table',
        name: 'Tabla de Datos',
        description: 'Lista detallada',
        icon: 'fas fa-table',
        type: 'DataTable'
      },
      {
        id: 'map-view',
        name: 'Vista de Mapa',
        description: 'Ubicaciones geográficas',
        icon: 'fas fa-map',
        type: 'MapView'
      },
      {
        id: 'activity-feed',
        name: 'Feed de Actividad',
        description: 'Actualizaciones recientes',
        icon: 'fas fa-stream',
        type: 'ActivityFeed'
      },
      {
        id: 'quick-stats',
        name: 'Estadísticas Rápidas',
        description: 'Resumen numérico',
        icon: 'fas fa-tachometer-alt',
        type: 'QuickStats'
      }
    ]
    
    const onDragStart = (event, component) => {
      draggedComponent.value = { ...component }
      event.dataTransfer.effectAllowed = 'copy'
    }
    
    const onDrop = (event) => {
      event.preventDefault()
      if (draggedComponent.value) {
        const newComponent = {
          ...draggedComponent.value,
          id: Date.now(),
          size: 'medium',
          props: getDefaultProps(draggedComponent.value.type)
        }
        dashboardComponents.value.push(newComponent)
        draggedComponent.value = null
      }
    }
    
    const getDefaultProps = (type) => {
      const defaultProps = {
        AnalyticsCard: {
          title: 'Métrica Clave',
          value: 0,
          change: 0,
          changePercent: '0%',
          subtitle: 'Último mes',
          color: 'blue'
        },
        BarChart: {
          title: 'Gráfico de Barras',
          data: [],
          labels: []
        },
        LineChart: {
          title: 'Tendencia',
          data: [],
          labels: []
        },
        PieChart: {
          title: 'Distribución',
          data: [],
          labels: []
        }
      }
      return defaultProps[type] || {}
    }
    
    const getComponentType = (type) => {
      const componentMap = {
        'AnalyticsCard': 'AnalyticsCard',
        'BarChart': 'BarChart',
        'LineChart': 'LineChart',
        'PieChart': 'PieChart'
      }
      return componentMap[type] || 'div'
    }
    
    const getComponentSize = (size) => {
      const sizeMap = {
        'small': 'col-span-1',
        'medium': 'col-span-2',
        'large': 'col-span-3'
      }
      return sizeMap[size] || 'col-span-2'
    }
    
    const getLayoutClass = () => {
      const layoutMap = {
        'grid': 'grid',
        'flex': 'flex',
        'custom': 'grid'
      }
      return layoutMap[selectedLayout.value] || 'grid'
    }
    
    const getGridClass = () => {
      return 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
    }
    
    const removeComponent = (index) => {
      dashboardComponents.value.splice(index, 1)
    }
    
    const editComponent = (component) => {
      editingComponent.value = { ...component }
      showSettingsModal.value = true
    }
    
    const saveComponentSettings = () => {
      const index = dashboardComponents.value.findIndex(c => c.id === editingComponent.value.id)
      if (index > -1) {
        dashboardComponents.value[index] = { ...editingComponent.value }
      }
      showSettingsModal.value = false
    }
    
    const resizeComponent = (component, size) => {
      component.size = size
    }
    
    const clearLayout = () => {
      dashboardComponents.value = []
    }
    
    const previewDashboard = () => {
      showPreviewModal.value = true
    }
    
    const saveDashboard = () => {
      const dashboardData = {
        layout: selectedLayout.value,
        components: dashboardComponents.value,
        timestamp: new Date().toISOString()
      }
      
      // Save to localStorage or API
      localStorage.setItem('customDashboard', JSON.stringify(dashboardData))
      
      // Show success message
      console.log('Dashboard guardado:', dashboardData)
    }
    
    return {
      dashboardComponents,
      selectedLayout,
      showSettingsModal,
      showPreviewModal,
      editingComponent,
      draggedComponent,
      availableComponents,
      onDragStart,
      onDrop,
      getComponentType,
      getComponentSize,
      getLayoutClass,
      getGridClass,
      removeComponent,
      editComponent,
      saveComponentSettings,
      resizeComponent,
      clearLayout,
      previewDashboard,
      saveDashboard
    }
  }
}
</script>
