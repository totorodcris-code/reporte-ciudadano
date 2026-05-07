<template>
  <div class="relative min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900">
    <!-- Fondo animado -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="relative z-10 min-h-screen">
      <!-- NAVBAR -->
      <header class="shadow-xl bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-50">
        <div class="container flex items-center justify-between px-6 py-4 mx-auto">
          <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 transition-transform rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 hover:scale-110">
              <span class="text-xl">📊</span>
            </div>
            <h1 class="text-xl font-bold text-white">
              Detalle de Reporte
            </h1>
          </div>

          <div class="flex items-center gap-4">
            <router-link
                :to="dashboardRoute"
                class="flex items-center gap-2 px-4 py-2 transition-all bg-primary-500/90 rounded-lg hover:bg-primary-600 hover:scale-105 backdrop-blur-sm text-white min-h-touch"
              >
                <i class="fas fa-arrow-left"></i>
                <span class="hidden md:inline">Volver al Dashboard</span>
              </router-link>

              <div class="hidden md:flex items-center gap-3 px-4 py-2 transition-all rounded-full bg-white/10 hover:bg-white/20 cursor-pointer" @click="$router.push('/perfil')">
                <div class="w-8 h-8 transition-transform rounded-full bg-gradient-to-br from-blue-400 to-purple-500 hover:scale-110 flex items-center justify-center text-white font-bold text-sm">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                </div>
                <span class="font-medium text-white">
                  {{ authStore.user?.name }}
                </span>
              </div>
              <button
                @click="logout"
                class="flex items-center gap-2 px-4 py-2 transition-all bg-red-500/90 rounded-lg hover:bg-red-600 hover:scale-105 backdrop-blur-sm text-white"
              >
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden md:inline">Cerrar Sesión</span>
              </button>
          </div>
        </div>
      </header>

      <!-- CONTENIDO -->
      <main class="container px-6 py-10 mx-auto text-white" v-if="!loading">
        <div v-if="incidencia" class="max-w-4xl mx-auto">
          <!-- Card principal -->
          <div class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <!-- Estado -->
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-3xl font-bold">{{ incidencia.titulo }}</h2>
              <span
                :class="[
                  'px-4 py-2 rounded-full text-sm font-medium',
                  incidencia.estado === 'resuelto'
                    ? 'bg-gradient-to-r from-green-500/20 to-green-600/20 text-green-300 border border-green-500/30'
                    : 'bg-gradient-to-r from-orange-500/20 to-orange-600/20 text-orange-300 border border-orange-500/30'
                ]"
              >
                {{ incidencia.estado === 'resuelto' ? '✅ Resuelto' : '⏳ Pendiente' }}
              </span>
            </div>

            <!-- Imagen -->
            <div v-if="incidencia.imagen" class="mb-6 rounded-xl overflow-hidden">
              <img :src="'/storage/' + incidencia.imagen" class="w-full max-h-96 object-cover" :alt="incidencia.titulo" />
            </div>

            <!-- Información -->
            <div class="grid gap-6 md:grid-cols-2">
              <div>
                <h3 class="mb-2 text-sm font-medium text-gray-400">Descripción</h3>
                <p class="text-gray-200">{{ incidencia.descripcion }}</p>
              </div>

              <div>
                <h3 class="mb-2 text-sm font-medium text-gray-400">Categoría</h3>
                <p class="text-yellow-400 font-medium">{{ incidencia.categoria?.nombre_categoria || 'Sin categoría' }}</p>
              </div>

              <div>
                <h3 class="mb-2 text-sm font-medium text-gray-400">Dirección</h3>
                <p class="text-gray-200">{{ incidencia.direccion }}</p>
              </div>

              <div>
                <h3 class="mb-2 text-sm font-medium text-gray-400">Fecha de reporte</h3>
                <p class="text-gray-200">{{ formatearFecha(incidencia.created_at) }}</p>
              </div>

              <div>
                <h3 class="mb-2 text-sm font-medium text-gray-400">Ubicación</h3>
                <p class="text-gray-200">
                  <i class="fas fa-map-marker-alt text-red-400 mr-2"></i>
                  {{ incidencia.latitud }}, {{ incidencia.longitud }}
                </p>
              </div>
            </div>

            <!-- Mapa -->
            <div class="mt-6">
              <h3 class="mb-2 text-sm font-medium text-gray-400">Ubicación en el mapa</h3>
              <div ref="map" class="w-full h-64 rounded-xl overflow-hidden border border-white/20"></div>
            </div>
          </div>
        </div>

        
        <!-- Error -->
        <div v-else class="text-center py-12">
          <div class="inline-block p-8 mb-4 transition-all rounded-full bg-white/10">
            <i class="text-6xl">❌</i>
          </div>
          <p class="mb-4 text-lg text-gray-400">No se encontró el reporte</p>
          <router-link
            :to="dashboardRoute"
            class="inline-flex items-center gap-2 px-6 py-3 font-medium text-yellow-400 hover:text-yellow-300 hover:scale-105 transition-all"
          >
            <i class="fas fa-arrow-left"></i> Volver al dashboard
          </router-link>
        </div>
      </main>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center min-h-screen">
        <div class="text-center">
          <i class="text-6xl text-yellow-400 fas fa-spinner fa-spin"></i>
          <p class="mt-4 text-lg text-gray-300">Cargando reporte...</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

export default {
  name: "DetalleIncidencia",
    data() {
    return {
      incidencia: null,
      loading: true,
      map: null
    }
  },

  computed: {
    authStore() {
      return useAuthStore()
    },
    dashboardRoute() {
      return this.authStore.isAdmin ? '/admin/dashboard' : '/dashboard'
    }
  },

  async mounted() {
    if (!this.authStore.isLoggedIn) {
      this.$router.push("/login")
      return
    }
    await this.cargarIncidencia()
  },

  methods: {
    async cargarIncidencia() {
      this.loading = true
      try {
        const id = this.$route.params.id
        const response = await api.get(`/incidencias/${id}`)
        
        // Manejar ambos formatos: API V1 (con success/data) y formato antiguo
        if (response.data.success && response.data.data) {
          // Nuevo formato API V1
          this.incidencia = response.data.data
        } else {
          // Formato antiguo o directo
          this.incidencia = response.data
        }

        this.$nextTick(() => {
          this.initMap()
        })
      } catch (error) {
        console.error('Error al cargar incidencia:', error)
        this.incidencia = null
      } finally {
        this.loading = false
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
        script.async = true
        document.head.appendChild(script)
        script.onload = () => this.createMap()
      } else {
        this.createMap()
      }
    },

    createMap() {
      const lat = parseFloat(this.incidencia.latitud)
      const lng = parseFloat(this.incidencia.longitud)

      if (isNaN(lat) || isNaN(lng)) {
        console.error('Coordenadas inválidas:', this.incidencia.latitud, this.incidencia.longitud)
        return
      }

      this.map = L.map(this.$refs.map).setView([lat, lng], 15)

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(this.map)

      L.marker([lat, lng]).addTo(this.map)
    },

    formatearFecha(fecha) {
      const date = new Date(fecha)
      return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    logout() {
      this.authStore.logout()
      this.$router.push("/login")
    }
  }
}
</script>

<style scoped>
.particles {
  background-image:
    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
  animation: float 20s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}
</style>
