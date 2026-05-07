<template>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 text-gray-900 p-4 sm:p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 sm:gap-4 mb-6 sm:mb-8 animate-slide-up">
      <router-link to="/dashboard" class="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 text-blue-600 hover:text-blue-700">
        <i class="fas fa-arrow-left text-sm sm:text-lg"></i>
      </router-link>
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">Nuevo Reporte</h1>
        <p class="text-gray-600 mt-1 text-sm sm:text-base">Reporta una incidencia en tu comunidad</p>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6 lg:p-8 animate-scale-in">
      <form @submit.prevent="guardarNuevoReporte" class="space-y-4 sm:space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
          <div>
            <label class="block mb-2 text-sm font-semibold text-gray-700">Título</label>
            <input v-model="nuevoReporte.titulo" type="text" class="input-field text-base sm:text-sm" placeholder="Breve descripción del problema" required />
          </div>

          <div>
            <label class="block mb-2 text-sm font-semibold text-gray-700">Categoría</label>
            <select v-model="nuevoReporte.categoria_id" class="input-field text-base sm:text-sm" required>
              <option value="" disabled>Selecciona una categoría</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">{{ cat.nombre }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Descripción</label>
          <textarea v-model="nuevoReporte.descripcion" rows="4" class="input-field resize-none text-base sm:text-sm" placeholder="Describe detalladamente la incidencia..." required></textarea>
        </div>

        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Ubicación *</label>
          <div ref="mapNew" class="w-full h-48 sm:h-56 lg:h-64 rounded-2xl border-2 border-gray-200 overflow-hidden shadow-inner"></div>
          <div v-if="nuevoReporte.direccion" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-xs sm:text-sm text-blue-700">
              <i class="fas fa-map-marker-alt mr-2"></i>
              {{ nuevoReporte.direccion }}
            </p>
          </div>
        </div>

        <div>
          <label class="block mb-2 text-sm font-semibold text-gray-700">Foto *</label>
          <div class="relative">
            <input 
              type="file" 
              accept="image/*" 
              @change="onFotoChange" 
              required 
              class="w-full h-12 sm:h-12 px-3 sm:px-4 py-2 sm:py-3 border-2 border-dashed border-gray-300 rounded-2xl text-gray-600 text-sm sm:text-base file:mr-3 sm:file:mr-4 file:py-2 file:px-3 sm:file:py-2 sm:file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all duration-200" 
            />
            <div v-if="!nuevoReporte.previewUrl" class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <i class="fas fa-camera text-gray-400 text-lg sm:text-xl"></i>
            </div>
          </div>
          <div v-if="nuevoReporte.previewUrl" class="mt-4 relative group">
            <img :src="nuevoReporte.previewUrl" class="w-full h-32 sm:h-40 lg:h-48 object-cover rounded-2xl shadow-lg" />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200 rounded-2xl"></div>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-4 sm:pt-6 border-t border-gray-200">
          <router-link 
            to="/dashboard" 
            class="px-4 py-3 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200 text-center sm:text-left order-2 sm:order-1"
          >
            Cancelar
          </router-link>
          <button 
            type="submit" 
            class="btn-accent order-1 sm:order-2 w-full sm:w-auto"
          >
            <i class="fas fa-paper-plane mr-2"></i>
            <span class="text-sm sm:text-base">Enviar Reporte</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"
import api from "../../services/api"

export default {
  name: "NuevaIncidencia",
  data() {
    return {
      categorias: [],
      mapNew: null,
      markerNew: null,
      nuevoReporte: {
        titulo: '',
        descripcion: '',
        categoria_id: null,
        latitud: '',
        longitud: '',
        direccion: '',
        foto: null,
        previewUrl: ''
      }
    }
  },
  computed: {
    authStore() {
      return useAuthStore()
    }
  },
  async mounted() {
    if (!this.authStore.isLoggedIn) {
      this.$router.push("/login")
      return
    }
    await this.cargarCategorias()
    this.initMap()
  },
  methods: {
    async cargarCategorias() {
      try {
        const response = await api.get('/categorias')
        this.categorias = response.data.map(c => ({
          id: c.id,
          nombre: c.nombre_categoria
        }))
      } catch (error) {
        console.error("Error:", error)
      }
    },
    initMap() {
      if (typeof L === 'undefined') {
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)
        const script = document.createElement('script')
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.onload = () => this.createMap()
        document.head.appendChild(script)
      } else {
        this.createMap()
      }
    },
    createMap() {
      const lat = -19.036
      const lng = -65.259
      this.mapNew = L.map(this.$refs.mapNew).setView([lat, lng], 15)
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.mapNew)
      this.markerNew = L.marker([lat, lng], { draggable: true }).addTo(this.mapNew)

      this.markerNew.on('dragend', async (e) => {
        const latlng = e.target.getLatLng()
        this.nuevoReporte.latitud = latlng.lat
        this.nuevoReporte.longitud = latlng.lng
        this.nuevoReporte.direccion = await this.obtenerDireccion(latlng.lat, latlng.lng)
      })

      this.mapNew.on('click', async (e) => {
        this.markerNew.setLatLng(e.latlng)
        this.nuevoReporte.latitud = e.latlng.lat
        this.nuevoReporte.longitud = e.latlng.lng
        this.nuevoReporte.direccion = await this.obtenerDireccion(e.latlng.lat, e.latlng.lng)
      })
    },
    async obtenerDireccion(lat, lng) {
      try {
        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        const data = await res.json()
        return data.display_name || ''
      } catch {
        return ''
      }
    },
    onFotoChange(e) {
      const file = e.target.files[0]
      if (file) {
        this.nuevoReporte.foto = file
        this.nuevoReporte.previewUrl = URL.createObjectURL(file)
      }
    },
    async guardarNuevoReporte() {
      if (!this.nuevoReporte.latitud || !this.nuevoReporte.foto) {
        const { useToast } = await import('@/Composables/useToast')
        const toast = useToast()
        toast.error('Falta ubicación o foto')
        return
      }
      
      try {
        const { useToast } = await import('@/Composables/useToast')
        const toast = useToast()
        
        // Show loading state
        const loadingToast = toast.info('Enviando reporte...', { 
          duration: 0, 
          autoClose: false,
          title: 'Procesando'
        })
        
        const formData = new FormData()
        formData.append('titulo', this.nuevoReporte.titulo)
        formData.append('descripcion', this.nuevoReporte.descripcion)
        formData.append('categoria_id', this.nuevoReporte.categoria_id)
        formData.append('latitud', this.nuevoReporte.latitud)
        formData.append('longitud', this.nuevoReporte.longitud)
        formData.append('direccion', this.nuevoReporte.direccion)
        formData.append('user_id', this.authStore.user.id)
        formData.append('imagen', this.nuevoReporte.foto)

        await api.post('/incidencias', formData)
        
        // Close loading toast
        await loadingToast
        
        // Show success message
        toast.success('Reporte creado exitosamente', {
          title: '¡Éxito!',
          duration: 4000
        })
        
        // Navigate after a short delay
        setTimeout(() => {
          this.$router.push('/dashboard')
        }, 1500)
        
      } catch (error) {
        console.error(error)
        const { useToast } = await import('@/Composables/useToast')
        const toast = useToast()
        toast.error('Error al crear el reporte. Por favor, intenta nuevamente.', {
          title: 'Error',
          duration: 5000
        })
      }
    },
    logout() {
      this.authStore.logout()
      this.$router.push("/login")
    }
  }
}
</script>
