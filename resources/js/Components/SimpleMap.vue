<template>
  <div class="relative">
    <!-- Contenedor del mapa -->
    <div 
      ref="mapContainer" 
      class="w-full h-96 rounded-lg overflow-hidden border border-white/20"
      :class="{ 'opacity-50': loading }"
    >
      <div v-if="loading" class="flex items-center justify-center h-full bg-gray-100">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin text-2xl text-gray-600 mb-2"></i>
          <p class="text-gray-600">Cargando mapa...</p>
        </div>
      </div>
    </div>

    <!-- Panel de información -->
    <div class="absolute bottom-4 left-4 z-10 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg p-3 max-w-xs">
      <div class="text-sm">
        <div class="font-semibold text-gray-800 mb-1">📍 Reportes en el mapa</div>
        <div class="text-gray-600">
          <span class="font-medium">{{ incidencias.length }}</span> reportes mostrados
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, onUnmounted, ref } from 'vue'

export default {
  name: 'SimpleMap',
  props: {
    incidencias: {
      type: Array,
      default: () => []
    },
    center: {
      type: Object,
      default: () => ({ lat: -19.036, lng: -65.259 })
    },
    zoom: {
      type: Number,
      default: 13
    }
  },

  setup(props) {
    const mapContainer = ref(null)
    const map = ref(null)
    const loading = ref(true)

    const initMap = async () => {
      if (!mapContainer.value) {
        console.error('Map container not found')
        return
      }

      try {
        loading.value = true
        console.log('Initializing simple map...')

        // Cargar Leaflet dinámicamente
        await loadLeaflet()
        console.log('Leaflet loaded')

        // Esperar un poco más para asegurar que el DOM esté listo
        await new Promise(resolve => setTimeout(resolve, 200))

        // Crear el mapa
        map.value = L.map(mapContainer.value, {
          center: [props.center.lat, props.center.lng],
          zoom: props.zoom
        })

        console.log('Map created')

        // Añadir capa de tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap contributors'
        }).addTo(map.value)

        console.log('Tile layer added')

        // Añadir marcadores
        addMarkers()

        loading.value = false
        console.log('Simple map initialized successfully')

      } catch (error) {
        console.error('Error initializing simple map:', error)
        loading.value = false
        
        // Mostrar mensaje de error
        if (mapContainer.value) {
          mapContainer.value.innerHTML = `
            <div class="flex items-center justify-center h-full bg-gray-100 rounded-lg p-4">
              <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-2"></i>
                <p class="text-gray-600">Error al cargar el mapa</p>
                <p class="text-sm text-gray-500 mt-1">Verifica tu conexión a internet</p>
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
        
        // Cargar CSS
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)

        // Cargar JS
        const script = document.createElement('script')
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.onload = () => {
          console.log('Leaflet loaded successfully')
          resolve()
        }
        script.onerror = reject
        document.head.appendChild(script)
      })
    }

    const addMarkers = () => {
      if (!map.value) return

      console.log(`Adding ${props.incidencias.length} markers`)

      props.incidencias.forEach((incidencia, index) => {
        if (incidencia.latitud && incidencia.longitud) {
          try {
            const marker = L.marker([parseFloat(incidencia.latitud), parseFloat(incidencia.longitud)])
              .addTo(map.value)
              .bindPopup(`
                <div class="p-2">
                  <strong>${incidencia.titulo}</strong><br>
                  <small>${incidencia.direccion || 'Sin dirección'}</small><br>
                  <small>Estado: ${incidencia.estado || 'pendiente'}</small>
                </div>
              `)
            
            console.log(`Marker ${index + 1} added: ${incidencia.titulo}`)
          } catch (error) {
            console.error(`Error adding marker ${index + 1}:`, error)
          }
        }
      })

      // Ajustar vista si hay marcadores
      const validIncidencias = props.incidencias.filter(i => i.latitud && i.longitud)
      if (validIncidencias.length > 0) {
        const group = new L.featureGroup(
          validIncidencias.map(i => 
            L.marker([parseFloat(i.latitud), parseFloat(i.longitud)])
          )
        )
        map.value.fitBounds(group.getBounds().pad(0.1))
        console.log('Map fitted to markers')
      }
    }

    onMounted(() => {
      console.log('SimpleMap mounted')
      setTimeout(() => {
        initMap()
      }, 300)
    })

    onUnmounted(() => {
      if (map.value) {
        map.value.remove()
        map.value = null
      }
    })

    return {
      mapContainer,
      loading
    }
  }
}
</script>

<style scoped>
/* Estilos básicos para el mapa */
</style>
