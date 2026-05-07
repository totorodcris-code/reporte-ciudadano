<template>
<div class="min-h-screen bg-gray-900 text-white p-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/admin/dashboard" class="text-yellow-400 hover:text-yellow-300">
        <i class="fas fa-arrow-left text-xl"></i>
      </router-link>
      <h1 class="text-2xl font-bold">Gestión de Incidencias</h1>
    </div>

    <div class="bg-gray-800 rounded-xl p-6">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-700">
            <th class="text-left py-3">Título</th>
            <th class="text-left py-3">Usuario</th>
            <th class="text-left py-3">Estado</th>
            <th class="text-left py-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="incidencia in incidencias" :key="incidencia.id" class="border-b border-gray-700/50 hover:bg-gray-700/30">
            <td class="py-3">{{ incidencia.titulo }}</td>
            <td class="py-3">{{ incidencia.user?.name || 'N/A' }}</td>
            <td class="py-3">
              <span :class="incidencia.estado === 'resuelto' ? 'text-green-400 font-bold' : 'text-orange-400 font-bold'">
                {{ incidencia.estado === 'resuelto' ? '✅ Resuelto' : '⏳ Pendiente' }}
              </span>
            </td>
            <td class="py-3">
              <select 
                :value="incidencia.estado" 
                @change="cambiarEstado(incidencia.id, $event.target.value)"
                :class="incidencia.estado === 'resuelto' ? 'bg-green-900/50 border-green-500 text-white' : 'bg-orange-900/50 border-orange-500 text-white'" 
                class="px-3 py-1 rounded-lg border"
              >
                <option value="pendiente">Pendiente</option>
                <option value="resuelto">Resuelto</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</template>

<script>
import api from "../../services/api"
import { useAuthStore } from "../../stores/auth"

export default {
  name: "GestionIncidencias",
  data() {
    return {
      incidencias: []
    }
  },
  mounted() {
    if (!useAuthStore().isAdmin) {
      this.$router.push('/dashboard')
      return
    }
    this.cargarIncidencias()
  },
  methods: {
    async cargarIncidencias() {
      try {
        const response = await api.get('/incidencias')
        this.incidencias = response.data.data || response.data // Handle pagination
      } catch (error) {
        console.error("Error:", error)
      }
    },
    async cambiarEstado(incidenciaId, nuevoEstado) {
      try {
        await api.put(`/incidencias/${incidenciaId}`, { estado: nuevoEstado })
        alert('✅ Estado actualizado')
        await this.cargarIncidencias()
      } catch (error) {
        console.error("Error:", error)
        alert('❌ Error al cambiar estado')
      }
    }
  }
}
</script>
