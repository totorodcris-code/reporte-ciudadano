<template>
  <div class="relative">
    <!-- Contenedor del mapa con altura fija -->
    <div 
      ref="mapContainer" 
      style="width: 100%; height: 400px; background-color: #f0f0f0; border: 2px solid #ccc;"
    >
      <div v-if="!mapLoaded" class="flex items-center justify-center h-full">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin text-2xl text-gray-600 mb-2"></i>
          <p class="text-gray-600">Cargando mapa...</p>
          <p class="text-sm text-gray-500 mt-2">Estado: {{ loadStatus }}</p>
        </div>
      </div>
    </div>

    <!-- Panel de información -->
    <div class="absolute top-4 right-4 z-[1000] bg-white/95 backdrop-blur-sm rounded-lg shadow-xl border border-gray-200">
      <div class="p-3">
        <div class="flex items-center justify-between gap-3">
          <div class="text-sm">
            <div class="font-bold text-gray-800 flex items-center gap-2">
              <i class="fas fa-map-marked-alt text-blue-600"></i>
              Reportes en el mapa
            </div>
            <div class="text-gray-700">
              <span class="font-medium text-blue-600">{{ incidencias.length }}</span> reportes
            </div>
          </div>
          <button 
            @click="toggleInfoPanel"
            class="p-1 text-gray-400 hover:text-gray-600 transition-colors"
            title="Minimizar panel"
          >
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, computed } from 'vue'

export default {
  name: 'BasicMap',
  props: {
    incidencias: {
      type: Array,
      default: () => []
    }
  },

  setup(props) {
    const mapContainer = ref(null)
    const mapLoaded = ref(false)
    const loadStatus = ref('Iniciando...')

    const incidenciasWithCoords = computed(() => {
      return props.incidencias.filter(i => i.latitud && i.longitud)
    })

    const initMap = async () => {
      console.log('=== INICIANDO MAPA BÁSICO ===')
      loadStatus.value = 'Verificando contenedor...'
      
      // Validar que el contenedor exista y esté en el DOM
      if (!mapContainer.value || !document.contains(mapContainer.value)) {
        console.error('❌ Contenedor del mapa no encontrado o no está en el DOM')
        loadStatus.value = 'Error: Contenedor no disponible'
        return
      }

      console.log('✅ Contenedor encontrado:', mapContainer.value)
      loadStatus.value = 'Cargando Leaflet...'

      try {
        // Método 1: Verificar si Leaflet ya está cargado
        if (window.L) {
          console.log('✅ Leaflet ya está cargado')
          loadStatus.value = 'Creando mapa...'
          await createMap()
        } else {
          console.log('📦 Cargando Leaflet...')
          loadStatus.value = 'Descargando Leaflet JS...'
          
          // Cargar Leaflet manualmente
          await loadLeafletManually()
        }
      } catch (error) {
        console.error('❌ Error en initMap:', error)
        loadStatus.value = `Error: ${error.message}`
        showErrorMessage(error.message)
      }
    }

    const loadLeafletManually = () => {
      return new Promise((resolve, reject) => {
        console.log('📦 Iniciando carga manual de Leaflet')
        
        // Validar que document.head esté disponible
        if (!document.head) {
          console.error('❌ document.head no está disponible')
          reject(new Error('DOM no disponible para cargar recursos'))
          return
        }
        
        // Cargar CSS primero
        loadStatus.value = 'Cargando CSS...'
        const cssLink = document.createElement('link')
        cssLink.rel = 'stylesheet'
        cssLink.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        cssLink.onload = () => {
          console.log('✅ CSS de Leaflet cargado')
          loadStatus.value = 'Cargando JavaScript...'
          loadJS()
        }
        cssLink.onerror = () => {
          console.error('❌ Error cargando CSS')
          reject(new Error('Error cargando CSS de Leaflet'))
        }
        document.head.appendChild(cssLink)

        const loadJS = () => {
          const script = document.createElement('script')
          script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
          script.onload = () => {
            console.log('✅ JavaScript de Leaflet cargado')
            
            // Esperar un poco más
            setTimeout(() => {
              if (window.L) {
                loadStatus.value = 'Creando mapa...'
                createMap().then(resolve).catch(reject)
              } else {
                reject(new Error('Leaflet no disponible después de cargar'))
              }
            }, 500)
          }
          script.onerror = () => {
            console.error('❌ Error cargando JavaScript')
            reject(new Error('Error cargando JavaScript de Leaflet'))
          }
          document.head.appendChild(script)
        }
      })
    }

    const createMap = () => {
      return new Promise((resolve, reject) => {
        console.log('🗺️ Creando mapa...')
        console.log('📊 Incidencias recibidas:', props.incidencias)
        console.log('📊 Incidencias con coordenadas:', incidenciasWithCoords.value)
        
        loadStatus.value = 'Inicializando mapa...'
        
        // Validar que el contenedor exista y esté en el DOM
        if (!mapContainer.value || !document.contains(mapContainer.value)) {
          console.error('❌ Contenedor del mapa no disponible para crear mapa')
          reject(new Error('Contenedor del mapa no disponible'))
          return
        }
        
        try {
          // Limpiar el contenedor de forma segura
          if (mapContainer.value.innerHTML) {
            mapContainer.value.innerHTML = ''
          }
          
          // Crear el mapa
          const map = L.map(mapContainer.value, {
            center: [-19.036, -65.259],
            zoom: 13
          })

          console.log('✅ Mapa creado')
          loadStatus.value = 'Añadiendo capa de tiles...'

          // Añadir capa de tiles
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
          }).addTo(map)

          console.log('✅ Capa de tiles añadida')
          loadStatus.value = 'Añadiendo marcadores...'

          // Añadir marcadores
          let markersAdded = 0
          props.incidencias.forEach((incidencia, index) => {
            console.log(`📍 Analizando incidencia ${index + 1}:`, {
              id: incidencia.id,
              titulo: incidencia.titulo,
              latitud: incidencia.latitud,
              longitud: incidencia.longitud,
              tieneCoords: !!(incidencia.latitud && incidencia.longitud)
            })
            
            if (incidencia.latitud && incidencia.longitud) {
              try {
                const lat = parseFloat(incidencia.latitud)
                const lng = parseFloat(incidencia.longitud)
                
                console.log(`🎯 Creando marcador en (${lat}, ${lng})`)
                
                const marker = L.marker([lat, lng]).addTo(map)
                
                // Debug: verificar qué datos tiene la incidencia
                console.log(`📍 Datos completos de incidencia ${index + 1}:`, incidencia)
                console.log(`📍 Campos específicos - ID: ${incidencia.id}, Título: ${incidencia.titulo}, Dirección: ${incidencia.direccion}, Estado: ${incidencia.estado}`)
                
                const direccion = incidencia.direccion && incidencia.direccion.trim() !== '' 
                    ? incidencia.direccion 
                    : 'Sin dirección registrada'
                
                console.log(`🔍 Valor final de dirección para popup: "${direccion}"`)
                
                // Construir HTML del popup con imagen
                let popupHTML = `
                  <div class="p-3 min-w-[250px] bg-white rounded">
                    <div class="font-bold text-gray-900 mb-2 text-base">${incidencia.titulo}</div>
                `
                
                // Agregar imagen si existe
                if (incidencia.imagen) {
                  popupHTML += `
                    <div class="mb-3">
                      <img 
                        src="/storage/${incidencia.imagen}" 
                        class="w-full h-32 object-cover rounded-lg shadow-md cursor-pointer hover:scale-105 transition-transform" 
                        alt="Imagen del reporte"
                        onclick="window.open('/storage/${incidencia.imagen}', '_blank')"
                        onerror="this.style.display='none'"
                      />
                    </div>
                  `
                }
                
                popupHTML += `
                    <div class="text-sm text-gray-600 mb-2">
                      <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                      ${direccion}
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs px-2 py-1 rounded-full font-medium ${
                        incidencia.estado === 'resuelto' ? 'bg-green-100 text-green-800' : 
                        incidencia.estado === 'en-progreso' ? 'bg-blue-100 text-blue-800' : 
                        'bg-orange-100 text-orange-800'
                      }">
                        ${incidencia.estado === 'resuelto' ? '✅ Resuelto' : 
                          incidencia.estado === 'en-progreso' ? '🔄 En Progreso' : 
                          '⏳ Pendiente'}
                      </span>
                    </div>
                  </div>
                `
                
                marker.bindPopup(popupHTML)
                
                markersAdded++
                console.log(`✅ Marcador ${index + 1} añadido: ${incidencia.titulo}`)
              } catch (error) {
                console.error(`❌ Error añadiendo marcador ${index + 1}:`, error)
              }
            } else {
              console.log(`⚠️ Incidencia ${index + 1} sin coordenadas válidas`)
            }
          })

          console.log(`✅ ${markersAdded} marcadores añadidos de ${props.incidencias.length} incidencias`)
          loadStatus.value = `Mapa listo (${markersAdded} marcadores)`

          // Ajustar vista si hay marcadores
          if (markersAdded > 0) {
            const bounds = L.latLngBounds(
              props.incidencias
                .filter(i => i.latitud && i.longitud)
                .map(i => [parseFloat(i.latitud), parseFloat(i.longitud)])
            )
            map.fitBounds(bounds.pad(0.1))
            console.log('✅ Vista ajustada a marcadores')
            
            // Forzar que los marcadores sean visibles
            setTimeout(() => {
              map.eachLayer((layer) => {
                if (layer instanceof L.Marker) {
                  // bringToFront no está disponible en esta versión, usamos setZIndex
                  if (layer.setZIndex) {
                    layer.setZIndex(1000)
                    console.log('📍 Marcador llevado al frente')
                  }
                }
              })
              map.invalidateSize()
            }, 200)
          } else {
            console.log('⚠️ No hay marcadores para ajustar la vista')
          }

          // Forzar actualización del mapa para asegurar que los marcadores sean visibles
          setTimeout(() => {
            if (map) {
              map.invalidateSize()
              console.log('🔄 Mapa size invalidado para forzar renderizado')
            }
          }, 100)

          mapLoaded.value = true
          console.log('🎉 Mapa completamente cargado')
          resolve()

        } catch (error) {
          console.error('❌ Error creando mapa:', error)
          reject(error)
        }
      })
    }

    const showErrorMessage = (message) => {
      if (mapContainer.value) {
        mapContainer.value.innerHTML = `
          <div class="flex items-center justify-center h-full bg-red-50 p-4">
            <div class="text-center">
              <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-2"></i>
              <p class="text-red-600 font-semibold">Error al cargar el mapa</p>
              <p class="text-sm text-gray-600 mt-1">${message}</p>
              <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Reintentar
              </button>
            </div>
          </div>
        `
      }
    }

    onMounted(() => {
      console.log('🚀 BasicMap montado')
      loadStatus.value = 'Componente montado, iniciando mapa...'
      
      // Esperar a que el DOM esté completamente listo
      setTimeout(() => {
        initMap()
      }, 1000)
    })

    const toggleInfoPanel = () => {
      const panel = document.querySelector('.absolute.top-4.right-4')
      if (panel) {
        panel.style.display = 'none'
      }
    }

    return {
      mapContainer,
      mapLoaded,
      loadStatus,
      incidenciasWithCoords,
      toggleInfoPanel
    }
  }
}
</script>

<style scoped>
/* Estilos mínimos */
</style>
