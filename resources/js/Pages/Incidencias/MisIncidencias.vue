<template>
<div class="min-h-screen bg-gray-900 text-white p-6">
  <div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/dashboard" class="text-yellow-400 hover:text-yellow-300">
        <i class="fas fa-arrow-left text-xl"></i>
      </router-link>
      <h1 class="text-2xl font-bold">Mis Reportes</h1>
    </div>

    <div v-if="loading" class="text-center py-12">
      <i class="fas fa-spinner fa-spin text-4xl text-yellow-400"></i>
    </div>

    <div v-else-if="reportes.length > 0" class="space-y-4">
      <div v-for="reporte in reportes" :key="reporte.id" class="bg-gray-800 p-4 rounded-xl border border-gray-700 hover:border-yellow-500 transition-all">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="font-bold text-lg">{{ reporte.titulo }}</h3>
            <p class="text-gray-400 text-sm mt-1">{{ reporte.direccion }}</p>
            <span :class="reporte.estado === 'resuelto' ? 'text-green-400' : 'text-orange-400'" class="text-sm mt-2 inline-block">
              {{ reporte.estado === 'resuelto' ? '✅ Resuelto' : '⏳ Pendiente' }}
            </span>
          </div>
          <router-link :to="`/reportes/${reporte.id}`" class="text-blue-400 hover:text-blue-300">
            <i class="fas fa-eye"></i> Ver
          </router-link>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-400">No tienes reportes aún</p>
      <router-link to="/nueva-incidencia" class="text-yellow-400 hover:text-yellow-300 mt-4 inline-block">
        Crear mi primer reporte
      </router-link>
    </div>
  </div>
</div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"
import api from "../../services/api"

export default {
  name: "MisIncidencias",
  data() {
    return {
      reportes: [],
      loading: true
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
    await this.cargarReportes()
  },
  methods: {
    async cargarReportes() {
      try {
        const response = await api.get('/incidencias')
        const userId = this.authStore.user?.id
        this.reportes = response.data.filter(r => r.user_id === userId)
      } catch (error) {
        console.error("Error:", error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
