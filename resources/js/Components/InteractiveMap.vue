<template>
  <div class="relative">
    <!-- Contenedor del mapa -->
    <div 
      ref="mapContainer" 
      class="w-full h-96 rounded-lg overflow-hidden border border-white/20"
      :class="{ 'opacity-50': loading }"
    ></div>

    <!-- Controles del mapa -->
    <div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
      <!-- Botón de ubicación actual -->
      <button
        @click="getCurrentLocation"
        :disabled="loadingLocation"
        class="p-3 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg hover:bg-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        title="Mi ubicación"
      >
        <i v-if="loadingLocation" class="fas fa-spinner fa-spin text-blue-600"></i>
        <i v-else class="fas fa-location-crosshairs text-blue-600"></i>
      </button>

      <!-- Botón de fullscreen -->
      <button
        @click="toggleFullscreen"
        class="p-3 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg hover:bg-white transition-all"
        title="Pantalla completa"
      >
        <i class="fas fa-expand text-gray-700"></i>
      </button>

      <!-- Control de zoom -->
      <div class="bg-white/90 backdrop-blur-sm rounded-lg shadow-lg">
        <button
          @click="zoomIn"
          class="p-2 hover:bg-gray-100 transition-colors rounded-t-lg"
          title="Acercar"
        >
          <i class="fas fa-plus text-gray-700"></i>
        </button>
        <div class="border-t border-gray-200"></div>
        <button
          @click="zoomOut"
          class="p-2 hover:bg-gray-100 transition-colors rounded-b-lg"
          title="Alejar"
        >
          <i class="fas fa-minus text-gray-700"></i>
        </button>
      </div>
    </div>

    <!-- Panel de información -->
    <div class="absolute bottom-4 left-4 z-10 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg p-3 max-w-xs">
      <div class="text-sm">
        <div class="font-semibold text-gray-800 mb-1">📍 Reportes en el mapa</div>
        <div class="text-gray-600">
          <span class="font-medium">{{ incidencias.length }}</span> reportes mostrados
          <span v-if="filteredCount !== incidencias.length">
            ({{ filteredCount }} visibles)
          </span>
        </div>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="loading" class="absolute inset-0 bg-black/50 flex items-center justify-center z-20 rounded-lg">
      <div class="text-white text-center">
        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
        <p class="text-sm">Cargando mapa...</p>
      </div>
    </div>

    <!-- Modal de detalles de incidencia -->
    <div
      v-if="selectedIncidencia"
      class="absolute inset-0 z-30 flex items-center justify-center bg-black/50 p-4"
      @click="closeDetails"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[80vh] overflow-y-auto"
        @click.stop
      >
        <div class="p-4">
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-bold text-gray-900">{{ selectedIncidencia.titulo }}</h3>
            <button @click="closeDetails" class="text-gray-400 hover:text-gray-600">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="space-y-3">
            <!-- Estado -->
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium text-gray-600">Estado:</span>
              <span
                :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  selectedIncidencia.estado === 'resuelto'
                    ? 'bg-green-100 text-green-800'
                    : selectedIncidencia.estado === 'en-progreso'
                    ? 'bg-blue-100 text-blue-800'
                    : 'bg-orange-100 text-orange-800'
                ]"
              >
                {{ getEstadoLabel(selectedIncidencia.estado) }}
              </span>
            </div>

            <!-- Categoría -->
            <div>
              <span class="text-sm font-medium text-gray-600">Categoría:</span>
              <span class="text-sm text-gray-900">{{ selectedIncidencia.categoria?.nombre_categoria || 'Sin categoría' }}</span>
            </div>

            <!-- Descripción -->
            <div>
              <span class="text-sm font-medium text-gray-600">Descripción:</span>
              <p class="text-sm text-gray-700 mt-1">{{ selectedIncidencia.descripcion }}</p>
            </div>

            <!-- Dirección -->
            <div>
              <span class="text-sm font-medium text-gray-600">Dirección:</span>
              <p class="text-sm text-gray-700 mt-1">{{ selectedIncidencia.direccion }}</p>
            </div>

            <!-- Fecha -->
            <div>
              <span class="text-sm font-medium text-gray-600">Reportado:</span>
              <p class="text-sm text-gray-700">{{ formatDate(selectedIncidencia.created_at) }}</p>
            </div>

            <!-- Imagen -->
            <div v-if="selectedIncidencia.imagen">
              <span class="text-sm font-medium text-gray-600">Imagen:</span>
              <img
                :src="getImageUrl(selectedIncidencia.imagen)"
                :alt="selectedIncidencia.titulo"
                class="w-full h-48 object-cover rounded-lg mt-2 cursor-pointer"
                @click="openImageModal(selectedIncidencia.imagen)"
              />
            </div>

            <!-- Acciones -->
            <div class="flex gap-2 pt-3 border-t">
              <button
                @click="verDetalles(selectedIncidencia.id)"
                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
              >
                <i class="fas fa-eye mr-1"></i> Ver detalles
              </button>
              <button
                @click="getDirections(selectedIncidencia)"
                class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
              >
                <i class="fas fa-directions mr-1"></i> Cómo llegar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de imagen -->
    <div
      v-if="imageModalUrl"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
      @click="closeImageModal"
    >
      <img
        :src="getImageUrl(imageModalUrl)"
        alt="Imagen ampliada"
        class="max-w-full max-h-full object-contain rounded-lg"
        @click.stop
      />
      <button
        @click="closeImageModal"
        class="absolute top-4 right-4 text-white bg-black/50 rounded-full p-2 hover:bg-black/70"
      >
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</template>

<script>
import { onMounted, onUnmounted, ref, computed, watch, nextTick } from 'vue'

export default {
  name: 'InteractiveMap',
  props: {
    incidencias: {
      type: Array,
      default: () => []
    },
    center: {
      type: Object,
      default: () => ({ lat: -19.036, lng: -65.259 }) // Sucre, Bolivia
    },
    zoom: {
      type: Number,
      default: 13
    },
    height: {
      type: String,
      default: '400px'
    },
    showControls: {
      type: Boolean,
      default: true
    },
    clusterMarkers: {
      type: Boolean,
      default: true
    }
  },

  setup(props, { emit }) {
    const mapContainer = ref(null)
    const map = ref(null)
    const markers = ref([])
    const userMarker = ref(null)
    const loading = ref(true)
    const loadingLocation = ref(false)
    const selectedIncidencia = ref(null)
    const imageModalUrl = ref(null)
    const isFullscreen = ref(false)

    // Computed properties
    const filteredCount = computed(() => {
      return markers.value.filter(marker => marker.getVisible()).length
    })

    // Métodos
    const initMap = async () => {
      if (!mapContainer.value) {
        console.error('Map container not found')
        return
      }

      try {
        loading.value = true
        console.log('Initializing map...')

        // Cargar Leaflet dinámicamente
        await loadLeaflet()
        console.log('Leaflet loaded successfully')

        // Esperar a que el DOM esté completamente listo
        await new Promise(resolve => setTimeout(resolve, 100))

        // Verificar que el contenedor tenga dimensiones
        const rect = mapContainer.value.getBoundingClientRect()
        if (rect.width === 0 || rect.height === 0) {
          console.warn('Map container has no dimensions, retrying...')
          await new Promise(resolve => setTimeout(resolve, 500))
        }

        // Crear el mapa
        map.value = L.map(mapContainer.value, {
          center: [props.center.lat, props.center.lng],
          zoom: props.zoom,
          zoomControl: false // Control personalizado
        })

        console.log('Map created successfully')

        // Añadir capa de tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors',
          maxZoom: 19
        }).addTo(map.value)

        console.log('Tile layer added')

        // Esperar a que el mapa esté listo
        map.value.whenReady(() => {
          console.log('Map is ready')
          loading.value = false
          addMarkers()
        })

        // Forzar invalidación y redimensionamiento
        setTimeout(() => {
          if (map.value) {
            map.value.invalidateSize()
            console.log('Map size invalidated')
          }
        }, 200)

      } catch (error) {
        console.error('Error initializing map:', error)
        loading.value = false
        
        // Mostrar mensaje de error al usuario
        if (mapContainer.value) {
          mapContainer.value.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg p-4">
              <div class="text-center">
                <i class="fas fa-map-marked-alt text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-600">No se pudo cargar el mapa</p>
                <button onclick="location.reload()" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                  Reintentar
                </button>
              </div>
            </div>
          `
        }
      }
    }

    const loadLeaflet = () => {
      return new Promise((resolve, reject) => {
        if (window.L) {
          console.log('Leaflet already loaded')
          resolve()
          return
        }

        console.log('Loading Leaflet...')
        
        // Timeout para evitar espera infinita
        const timeout = setTimeout(() => {
          reject(new Error('Timeout loading Leaflet'))
        }, 10000)

        // Cargar CSS
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        link.onload = () => console.log('Leaflet CSS loaded')
        link.onerror = () => console.warn('Failed to load Leaflet CSS')
        document.head.appendChild(link)

        // Cargar JS
        const script = document.createElement('script')
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.async = true
        
        script.onload = () => {
          clearTimeout(timeout)
          if (window.L) {
            console.log('Leaflet JS loaded successfully')
            resolve()
          } else {
            reject(new Error('Leaflet not available after load'))
          }
        }
        
        script.onerror = (error) => {
          clearTimeout(timeout)
          console.error('Failed to load Leaflet JS:', error)
          reject(error)
        }
        
        document.head.appendChild(script)
      })
    }

    const addMarkers = () => {
      if (!map.value) {
        console.error('Map not available for adding markers')
        return
      }

      console.log(`Adding markers for ${props.incidencias.length} incidencias`)

      // Limpiar marcadores existentes
      clearMarkers()

      let validMarkers = 0
      // Añadir marcadores de incidencias
      props.incidencias.forEach((incidencia, index) => {
        if (incidencia.latitud && incidencia.longitud && 
            !isNaN(parseFloat(incidencia.latitud)) && 
            !isNaN(parseFloat(incidencia.longitud))) {
          
          console.log(`Adding marker ${index + 1}: ${incidencia.titulo} at (${incidencia.latitud}, ${incidencia.longitud})`)
          
          try {
            const marker = createIncidenciaMarker(incidencia)
            markers.value.push(marker)
            validMarkers++
          } catch (error) {
            console.error(`Error creating marker for incidencia ${incidencia.id}:`, error)
          }
        } else {
          console.warn(`Invalid coordinates for incidencia ${incidencia.id}:`, {
            latitud: incidencia.latitud,
            longitud: incidencia.longitud
          })
        }
      })

      console.log(`Successfully added ${validMarkers} markers`)

      // Ajustar vista a los marcadores si hay marcadores válidos
      if (markers.value.length > 0) {
        try {
          const group = new L.featureGroup(markers.value)
          const bounds = group.getBounds()
          
          if (bounds.isValid()) {
            map.value.fitBounds(bounds.pad(0.1))
            console.log('Map fitted to markers bounds')
          } else {
            console.warn('Invalid bounds, using default center')
            map.value.setView([props.center.lat, props.center.lng], props.zoom)
          }
        } catch (error) {
          console.error('Error fitting map bounds:', error)
          map.value.setView([props.center.lat, props.center.lng], props.zoom)
        }
      } else {
        console.log('No markers to display, using default view')
        map.value.setView([props.center.lat, props.center.lng], props.zoom)
      }
    }

    const createIncidenciaMarker = (incidencia) => {
      try {
        const icon = L.divIcon({
          html: `
            <div class="relative">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-lg
                ${incidencia.estado === 'resuelto' ? 'bg-green-500' : 
                  incidencia.estado === 'en-progreso' ? 'bg-blue-500' : 'bg-orange-500'}">
                <i class="fas ${getEstadoIcon(incidencia.estado)}"></i>
              </div>
              <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-black/20 rounded-full"></div>
            </div>
          `,
          className: 'custom-marker',
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32]
        })

        const marker = L.marker([parseFloat(incidencia.latitud), parseFloat(incidencia.longitud)], { icon })
        
        // Popup personalizado
        const popupContent = `
          <div class="p-2 min-w-[200px]">
            <div class="font-semibold text-sm mb-1">${incidencia.titulo}</div>
            <div class="text-xs text-gray-600 mb-2">${incidencia.direccion || 'Sin dirección'}</div>
            <div class="flex justify-between items-center">
              <span class="text-xs px-2 py-1 rounded-full ${
                incidencia.estado === 'resuelto' ? 'bg-green-100 text-green-800' : 
                incidencia.estado === 'en-progreso' ? 'bg-blue-100 text-blue-800' : 
                'bg-orange-100 text-orange-800'
              }">
                ${getEstadoLabel(incidencia.estado)}
              </span>
              <button onclick="window.mapComponent.showDetails(${incidencia.id})" 
                      class="text-xs text-blue-600 hover:text-blue-800">
                Ver más
              </button>
            </div>
          </div>
        `
        
        marker.bindPopup(popupContent)
        marker.addTo(map.value)

        // Evento click
        marker.on('click', () => {
          selectedIncidencia.value = incidencia
        })

        return marker
      } catch (error) {
        console.error(`Error creating marker for incidencia ${incidencia.id}:`, error)
        throw error
      }
    }

    const clearMarkers = () => {
      markers.value.forEach(marker => {
        map.value.removeLayer(marker)
      })
      markers.value = []

      if (userMarker.value) {
        map.value.removeLayer(userMarker.value)
        userMarker.value = null
      }
    }

    const getCurrentLocation = async () => {
      if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización')
        return
      }

      loadingLocation.value = true

      try {
        const position = await new Promise((resolve, reject) => {
          navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
          })
        })

        const { latitude, longitude } = position.coords

        // Crear marcador de ubicación del usuario
        if (userMarker.value) {
          map.value.removeLayer(userMarker.value)
        }

        const userIcon = L.divIcon({
          html: `
            <div class="relative">
              <div class="w-6 h-6 bg-blue-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center">
                <div class="w-2 h-2 bg-white rounded-full"></div>
              </div>
              <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-blue-300 rounded-full animate-ping"></div>
            </div>
          `,
          className: 'user-marker',
          iconSize: [24, 24],
          iconAnchor: [12, 24]
        })

        userMarker.value = L.marker([latitude, longitude], { icon: userIcon })
          .addTo(map.value)
          .bindPopup('Tu ubicación actual')

        // Centrar mapa en la ubicación del usuario
        map.value.setView([latitude, longitude], 15)

      } catch (error) {
        console.error('Error getting location:', error)
        alert('No se pudo obtener tu ubicación')
      } finally {
        loadingLocation.value = false
      }
    }

    const zoomIn = () => {
      if (map.value) map.value.zoomIn()
    }

    const zoomOut = () => {
      if (map.value) map.value.zoomOut()
    }

    const toggleFullscreen = () => {
      if (!document.fullscreenElement) {
        mapContainer.value.requestFullscreen()
        isFullscreen.value = true
      } else {
        document.exitFullscreen()
        isFullscreen.value = false
      }
    }

    const showDetails = (incidenciaId) => {
      const incidencia = props.incidencias.find(i => i.id === incidenciaId)
      if (incidencia) {
        selectedIncidencia.value = incidencia
      }
    }

    const closeDetails = () => {
      selectedIncidencia.value = null
    }

    const openImageModal = (imageUrl) => {
      imageModalUrl.value = imageUrl
    }

    const closeImageModal = () => {
      imageModalUrl.value = null
    }

    const getEstadoLabel = (estado) => {
      const labels = {
        'pendiente': 'Pendiente',
        'en-progreso': 'En Progreso',
        'resuelto': 'Resuelto'
      }
      return labels[estado] || estado
    }

    const getEstadoIcon = (estado) => {
      const icons = {
        'pendiente': 'fa-clock',
        'en-progreso': 'fa-spinner',
        'resuelto': 'fa-check'
      }
      return icons[estado] || 'fa-exclamation'
    }

    const getImageUrl = (imagePath) => {
      if (!imagePath) return ''
      return `/storage/${imagePath}`
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const verDetalles = (incidenciaId) => {
      emit('ver-detalles', incidenciaId)
      closeDetails()
    }

    const getDirections = (incidencia) => {
      if (incidencia.latitud && incidencia.longitud) {
        const url = `https://www.google.com/maps/dir/?api=1&destination=${incidencia.latitud},${incidencia.longitud}`
        window.open(url, '_blank')
      }
    }

    // Watchers
    watch(() => props.incidencias, () => {
      if (map.value && !loading.value) {
        addMarkers()
      }
    }, { deep: true })

    // Lifecycle
    onMounted(() => {
      console.log('InteractiveMap mounted')
      
      // Hacer el componente disponible globalmente para los popups
      window.mapComponent = { showDetails }
      
      // Esperar a que el DOM esté completamente renderizado
      nextTick(() => {
        setTimeout(() => {
          initMap()
        }, 100)
      })
    })

    onUnmounted(() => {
      console.log('InteractiveMap unmounting')
      
      if (window.mapComponent) {
        delete window.mapComponent
      }
      
      if (map.value) {
        map.value.remove()
        map.value = null
      }
      
      // Limpiar marcadores
      markers.value = []
      userMarker.value = null
    })

    return {
      mapContainer,
      loading,
      loadingLocation,
      selectedIncidencia,
      imageModalUrl,
      filteredCount,
      getCurrentLocation,
      zoomIn,
      zoomOut,
      toggleFullscreen,
      closeDetails,
      closeImageModal,
      getEstadoLabel,
      getImageUrl,
      formatDate,
      verDetalles,
      getDirections
    }
  }
}
</script>

<style scoped>
.custom-marker {
  background: transparent !important;
  border: none !important;
}

.user-marker {
  background: transparent !important;
  border: none !important;
}

/* Animaciones */
@keyframes ping {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  75%, 100% {
    transform: scale(1.5);
    opacity: 0;
  }
}

.animate-ping {
  animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Estilos para fullscreen */
:fullscreen .relative {
  background: white;
}
</style>
