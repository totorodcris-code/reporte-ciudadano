<template>
<div class="min-h-screen bg-gray-900 text-white p-6">
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
      <router-link to="/admin/dashboard" class="text-yellow-400 hover:text-yellow-300">
        <i class="fas fa-arrow-left text-xl"></i>
      </router-link>
      <h1 class="text-2xl font-bold">Gestión de Usuarios</h1>
    </div>

    <div class="bg-gray-800 rounded-xl p-6">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-700">
            <th class="text-left py-3">Nombre</th>
            <th class="text-left py-3">Email</th>
            <th class="text-left py-3">Rol</th>
            <th class="text-left py-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.id" class="border-b border-gray-700/50 hover:bg-gray-700/30">
            <td class="py-3">{{ usuario.name }}</td>
            <td class="py-3">{{ usuario.email }}</td>
            <td class="py-3">
              <span :class="usuario.role === 'admin' ? 'text-red-400 font-bold' : 'text-green-400 font-bold'">
                {{ usuario.role === 'admin' ? '👑 Admin' : '👤 Usuario' }}
              </span>
            </td>
            <td class="py-3">
              <select 
                :value="usuario.role" 
                @change="cambiarRol(usuario.id, $event.target.value)"
                :class="usuario.role === 'admin' ? 'bg-red-900/50 border-red-500 text-white' : 'bg-green-900/50 border-green-500 text-white'" 
                class="px-3 py-1 rounded-lg border"
              >
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
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
  name: "GestionUsuarios",
  data() {
    return {
      usuarios: []
    }
  },
  mounted() {
    if (!useAuthStore().isAdmin) {
      this.$router.push('/dashboard')
      return
    }
    this.cargarUsuarios()
  },
  methods: {
    async cargarUsuarios() {
      try {
        const response = await api.get('/usuarios')
        this.usuarios = response.data
      } catch (error) {
        console.error("Error:", error)
      }
    },
    async cambiarRol(userId, nuevoRol) {
      try {
        await api.put(`/usuarios/${userId}/role`, { role: nuevoRol })
        alert('✅ Rol actualizado')
        await this.cargarUsuarios()
      } catch (error) {
        console.error("Error:", error)
        alert('❌ Error al cambiar rol')
      }
    }
  }
}
</script>
