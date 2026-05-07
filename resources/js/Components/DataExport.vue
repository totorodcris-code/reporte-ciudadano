<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="btn-secondary flex items-center gap-2"
      :disabled="isExporting"
    >
      <i v-if="!isExporting" class="fas fa-download"></i>
      <LoadingSpinner v-else size="sm" />
      <span>Exportar Datos</span>
      <i class="fas fa-chevron-down ml-2 transition-transform duration-200" :class="{ 'rotate-180': showDropdown }"></i>
    </button>
    
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="showDropdown"
        class="absolute right-0 top-full mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 min-w-64"
      >
        <div class="p-2 border-b border-gray-100">
          <h3 class="text-sm font-semibold text-gray-900 px-2 py-1">Exportar como</h3>
        </div>
        
        <div class="py-1">
          <button
            v-for="format in exportFormats"
            :key="format.key"
            @click="exportData(format)"
            class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center justify-between"
            :disabled="isExporting"
          >
            <div class="flex items-center gap-3">
              <i :class="format.icon + ' text-gray-400'"></i>
              <span>{{ format.label }}</span>
            </div>
            <span class="text-xs text-gray-500">{{ format.description }}</span>
          </button>
        </div>
        
        <!-- Export options -->
        <div class="border-t border-gray-100 p-3">
          <h4 class="text-sm font-medium text-gray-900 mb-3">Opciones de exportación</h4>
          
          <div class="space-y-3">
            <!-- Date range -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">Rango de fechas</label>
              <select v-model="exportOptions.dateRange" class="input-field text-xs">
                <option value="all">Todos los datos</option>
                <option value="last_7_days">Últimos 7 días</option>
                <option value="last_30_days">Últimos 30 días</option>
                <option value="last_90_days">Últimos 90 días</option>
                <option value="custom">Personalizado</option>
              </select>
            </div>
            
            <!-- Custom date inputs -->
            <div v-if="exportOptions.dateRange === 'custom'" class="grid grid-cols-2 gap-2">
              <input
                v-model="exportOptions.dateFrom"
                type="date"
                class="input-field text-xs"
                placeholder="Desde"
              />
              <input
                v-model="exportOptions.dateTo"
                type="date"
                class="input-field text-xs"
                placeholder="Hasta"
              />
            </div>
            
            <!-- Include columns -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-2">Columnas a incluir</label>
              <div class="space-y-2 max-h-32 overflow-y-auto">
                <label v-for="column in availableColumns" :key="column.key" class="flex items-center gap-2">
                  <input
                    type="checkbox"
                    :value="column.key"
                    v-model="exportOptions.columns"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 text-xs"
                  />
                  <span class="text-xs text-gray-700">{{ column.label }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Action buttons -->
        <div class="border-t border-gray-100 p-3 flex gap-2">
          <button
            @click="showDropdown = false"
            class="flex-1 px-3 py-2 text-xs text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg transition-colors duration-200"
          >
            Cancelar
          </button>
          <button
            @click="startExport"
            class="flex-1 px-3 py-2 text-xs text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200"
            :disabled="isExporting || !hasValidOptions"
          >
            <LoadingSpinner v-if="isExporting" size="xs" />
            <span v-else>Exportar</span>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useApi } from '@/Composables/useApi'
import LoadingSpinner from '@/Components/LoadingSpinner.vue'

export default {
  name: 'DataExport',
  components: {
    LoadingSpinner
  },
  props: {
    data: {
      type: Array,
      default: () => []
    },
    exportEndpoint: {
      type: String,
      default: '/api/export'
    },
    availableColumns: {
      type: Array,
      default: () => [
        { key: 'id', label: 'ID' },
        { key: 'titulo', label: 'Título' },
        { key: 'descripcion', label: 'Descripción' },
        { key: 'categoria', label: 'Categoría' },
        { key: 'estado', label: 'Estado' },
        { key: 'fecha_creacion', label: 'Fecha de creación' },
        { key: 'usuario', label: 'Usuario' }
      ]
    }
  },
  emits: ['export-complete', 'export-error'],
  setup(props, { emit }) {
    const showDropdown = ref(false)
    const isExporting = ref(false)
    const exportProgress = ref(0)
    
    const exportFormats = [
      {
        key: 'csv',
        label: 'CSV',
        description: 'Valores separados por comas',
        icon: 'fas fa-file-csv'
      },
      {
        key: 'excel',
        label: 'Excel',
        description: 'Hoja de cálculo de Microsoft',
        icon: 'fas fa-file-excel'
      },
      {
        key: 'pdf',
        label: 'PDF',
        description: 'Documento de formato portable',
        icon: 'fas fa-file-pdf'
      },
      {
        key: 'json',
        label: 'JSON',
        description: 'Formato de datos estructurados',
        icon: 'fas fa-file-code'
      }
    ]
    
    const exportOptions = ref({
      dateRange: 'all',
      dateFrom: '',
      dateTo: '',
      columns: props.availableColumns.map(col => col.key)
    })
    
    const hasValidOptions = computed(() => {
      if (exportOptions.value.dateRange === 'custom') {
        return exportOptions.value.dateFrom && exportOptions.value.dateTo && exportOptions.value.columns.length > 0
      }
      return exportOptions.value.columns.length > 0
    })
    
    const toggleDropdown = () => {
      showDropdown.value = !showDropdown.value
    }
    
    const exportData = async (format) => {
      exportOptions.value.format = format.key
      await startExport()
    }
    
    const startExport = async () => {
      if (!hasValidOptions.value || isExporting.value) return
      
      isExporting.value = true
      showDropdown.value = false
      
      try {
        const { api } = useApi()
        const response = await api.post(props.exportEndpoint, {
          format: exportOptions.value.format,
          dateRange: exportOptions.value.dateRange,
          dateFrom: exportOptions.value.dateFrom,
          dateTo: exportOptions.value.dateTo,
          columns: exportOptions.value.columns
        })
        
        const blob = await response.blob()
        const url = window.URL.createObjectURL(blob)
        
        // Create download link
        const link = document.createElement('a')
        link.href = url
        link.download = `incidencias_${exportOptions.value.format}_${new Date().toISOString().split('T')[0]}.${exportOptions.value.format}`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        
        // Clean up
        window.URL.revokeObjectURL(url)
        
        emit('export-complete', {
          format: exportOptions.value.format,
          size: blob.size
        })
        
      } catch (error) {
        console.error('Export error:', error)
        emit('export-error', error)
      } finally {
        isExporting.value = false
      }
    }
    
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showDropdown.value = false
      }
    }
    
    onMounted(() => {
      document.addEventListener('click', handleClickOutside)
    })
    
    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })
    
    return {
      showDropdown,
      isExporting,
      exportProgress,
      exportFormats,
      exportOptions,
      hasValidOptions,
      toggleDropdown,
      exportData,
      startExport
    }
  }
}
</script>
