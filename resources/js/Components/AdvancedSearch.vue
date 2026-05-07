<template>
  <div class="relative">
    <div class="relative">
      <div class="flex items-center gap-3">
        <div class="relative flex-1">
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            :placeholder="placeholder"
            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white text-gray-900 placeholder-gray-500"
            @input="onSearchInput"
            @keydown.enter="performSearch"
            @keydown.escape="clearSearch"
            @focus="showSuggestions = true"
          />
          
          <!-- Search icon -->
          <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
            <i v-if="!isSearching" class="fas fa-search"></i>
            <LoadingSpinner v-else size="sm" color="text-gray-400" />
          </div>
        </div>
        
        <!-- Filter button -->
        <button
          @click="toggleFilters"
          class="px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors duration-200 min-h-touch"
          :class="{ 'bg-blue-100 text-blue-700': hasActiveFilters }"
        >
          <i class="fas fa-filter"></i>
          <span class="hidden sm:inline ml-2">Filtros</span>
          <Badge v-if="activeFiltersCount > 0" :count="activeFiltersCount" size="sm" variant="primary" />
        </button>
      </div>
      
      <!-- Search suggestions dropdown -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
      >
        <div
          v-if="showSuggestions && (suggestions.length > 0 || recentSearches.length > 0)"
          class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 max-h-96 overflow-y-auto"
        >
          <!-- Recent searches -->
          <div v-if="recentSearches.length > 0 && !searchQuery" class="p-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Búsquedas recientes</h3>
            <div class="space-y-2">
              <button
                v-for="recent in recentSearches"
                :key="recent"
                @click="selectRecentSearch(recent)"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-150"
              >
                <i class="fas fa-history mr-2 text-gray-400"></i>
                {{ recent }}
              </button>
            </div>
          </div>
          
          <!-- Search suggestions -->
          <div v-if="suggestions.length > 0" class="p-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Sugerencias</h3>
            <div class="space-y-2">
              <button
                v-for="suggestion in suggestions"
                :key="suggestion.text"
                @click="selectSuggestion(suggestion)"
                class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-150"
              >
                <i :class="suggestion.icon + ' mr-2 text-gray-400'"></i>
                <span v-html="highlightMatch(suggestion.text)"></span>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
    
    <!-- Advanced filters panel -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-2 opacity-0"
    >
      <div
        v-if="showFilters"
        class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-6 min-w-80"
      >
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtros avanzados</h3>
        
        <div class="space-y-4">
          <!-- Category filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
            <select v-model="filters.category" class="input-field">
              <option value="">Todas las categorías</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nombre }}
              </option>
            </select>
          </div>
          
          <!-- Status filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select v-model="filters.status" class="input-field">
              <option value="">Todos los estados</option>
              <option value="pending">Pendiente</option>
              <option value="in_progress">En progreso</option>
              <option value="resolved">Resuelto</option>
              <option value="closed">Cerrado</option>
            </select>
          </div>
          
          <!-- Date range filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rango de fechas</label>
            <div class="grid grid-cols-2 gap-3">
              <input
                v-model="filters.dateFrom"
                type="date"
                class="input-field"
                placeholder="Desde"
              />
              <input
                v-model="filters.dateTo"
                type="date"
                class="input-field"
                placeholder="Hasta"
              />
            </div>
          </div>
          
          <!-- Priority filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label>
            <div class="flex gap-3">
              <label v-for="priority in priorities" :key="priority.value" class="flex items-center">
                <input
                  type="checkbox"
                  :value="priority.value"
                  v-model="filters.priorities"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="ml-2 text-sm text-gray-700">{{ priority.label }}</span>
              </label>
            </div>
          </div>
        </div>
        
        <!-- Filter actions -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
          <button
            @click="clearFilters"
            class="text-sm text-gray-600 hover:text-gray-800 transition-colors duration-200"
          >
            Limpiar filtros
          </button>
          <div class="flex gap-3">
            <button
              @click="cancelFilters"
              class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg transition-colors duration-200"
            >
              Cancelar
            </button>
            <button
              @click="applyFilters"
              class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200"
            >
              Aplicar filtros
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useApi } from '@/Composables/useApi'
import LoadingSpinner from '@/Components/LoadingSpinner.vue'
import Badge from '@/Components/Badge.vue'

export default {
  name: 'AdvancedSearch',
  components: {
    LoadingSpinner,
    Badge
  },
  props: {
    placeholder: {
      type: String,
      default: 'Buscar incidencias...'
    },
    categories: {
      type: Array,
      default: () => []
    },
    searchEndpoint: {
      type: String,
      default: '/api/incidencias/search'
    }
  },
  emits: ['search', 'filter'],
  setup(props, { emit }) {
    const searchQuery = ref('')
    const isSearching = ref(false)
    const showSuggestions = ref(false)
    const suggestions = ref([])
    const recentSearches = ref([])
    const showFilters = ref(false)
    const filters = ref({
      category: '',
      status: '',
      dateFrom: '',
      dateTo: '',
      priorities: []
    })
    
    const priorities = [
      { value: 'low', label: 'Baja' },
      { value: 'medium', label: 'Media' },
      { value: 'high', label: 'Alta' },
      { value: 'urgent', label: 'Urgente' }
    ]
    
    const searchInput = ref(null)
    let searchTimeout = null
    let clickOutsideHandler = null
    
    const activeFiltersCount = computed(() => {
      let count = 0
      if (filters.value.category) count++
      if (filters.value.status) count++
      if (filters.value.dateFrom || filters.value.dateTo) count++
      if (filters.value.priorities.length > 0) count++
      return count
    })
    
    const hasActiveFilters = computed(() => activeFiltersCount.value > 0)
    
    const onSearchInput = () => {
      clearTimeout(searchTimeout)
      
      if (searchQuery.value.length < 2) {
        suggestions.value = []
        return
      }
      
      searchTimeout = setTimeout(async () => {
        await fetchSuggestions()
      }, 300)
    }
    
    const fetchSuggestions = async () => {
      try {
        isSearching.value = true
        const { api } = useApi()
        const response = await api.get(`${props.searchEndpoint}?q=${encodeURIComponent(searchQuery.value)}`)
        const data = await response.json()
        
        suggestions.value = data.suggestions || []
      } catch (error) {
        console.error('Error fetching suggestions:', error)
        suggestions.value = []
      } finally {
        isSearching.value = false
      }
    }
    
    const performSearch = () => {
      if (searchQuery.value.trim()) {
        addToRecentSearches(searchQuery.value.trim())
        emit('search', {
          query: searchQuery.value.trim(),
          filters: { ...filters.value }
        })
        showSuggestions.value = false
      }
    }
    
    const selectSuggestion = (suggestion) => {
      searchQuery.value = suggestion.text
      performSearch()
    }
    
    const selectRecentSearch = (query) => {
      searchQuery.value = query
      performSearch()
    }
    
    const clearSearch = () => {
      searchQuery.value = ''
      suggestions.value = []
      showSuggestions.value = false
    }
    
    const toggleFilters = () => {
      showFilters.value = !showFilters.value
      showSuggestions.value = false
    }
    
    const applyFilters = () => {
      emit('filter', { ...filters.value })
      showFilters.value = false
    }
    
    const cancelFilters = () => {
      showFilters.value = false
    }
    
    const clearFilters = () => {
      filters.value = {
        category: '',
        status: '',
        dateFrom: '',
        dateTo: '',
        priorities: []
      }
    }
    
    const highlightMatch = (text) => {
      if (!searchQuery.value) return text
      
      const regex = new RegExp(`(${searchQuery.value})`, 'gi')
      return text.replace(regex, '<mark class="bg-yellow-200 text-yellow-800">$1</mark>')
    }
    
    const addToRecentSearches = (query) => {
      const existing = recentSearches.value.indexOf(query)
      if (existing > -1) {
        recentSearches.value.splice(existing, 1)
      }
      
      recentSearches.value.unshift(query)
      if (recentSearches.value.length > 5) {
        recentSearches.value = recentSearches.value.slice(0, 5)
      }
      
      localStorage.setItem('recentSearches', JSON.stringify(recentSearches.value))
    }
    
    const loadRecentSearches = () => {
      try {
        const saved = localStorage.getItem('recentSearches')
        if (saved) {
          recentSearches.value = JSON.parse(saved)
        }
      } catch (error) {
        console.error('Error loading recent searches:', error)
      }
    }
    
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        showSuggestions.value = false
        showFilters.value = false
      }
    }
    
    watch(searchQuery, (newValue) => {
      if (newValue.length === 0) {
        suggestions.value = []
      }
    })
    
    onMounted(() => {
      loadRecentSearches()
      clickOutsideHandler = handleClickOutside
      document.addEventListener('click', clickOutsideHandler)
    })
    
    onUnmounted(() => {
      if (clickOutsideHandler) {
        document.removeEventListener('click', clickOutsideHandler)
      }
      clearTimeout(searchTimeout)
    })
    
    return {
      searchQuery,
      isSearching,
      showSuggestions,
      suggestions,
      recentSearches,
      showFilters,
      filters,
      priorities,
      activeFiltersCount,
      hasActiveFilters,
      searchInput,
      onSearchInput,
      performSearch,
      selectSuggestion,
      selectRecentSearch,
      clearSearch,
      toggleFilters,
      applyFilters,
      cancelFilters,
      clearFilters,
      highlightMatch
    }
  }
}
</script>

<style scoped>
mark {
  padding: 1px 2px;
  border-radius: 3px;
  font-weight: 600;
}
</style>
