<template>
  <div class="relative min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900">
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <div class="relative z-10 min-h-screen">
      <!-- NAVBAR -->
      <header class="shadow-xl bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-50">
        <div class="container flex items-center justify-between px-6 py-4 mx-auto">
          <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 transition-transform rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 hover:scale-110">
              <span class="text-xl">👤</span>
            </div>
            <h1 class="text-xl font-bold text-white">Mi Perfil</h1>
          </div>

          <div class="flex items-center gap-4">
            <router-link :to="dashboardRoute" class="flex items-center gap-2 px-4 py-2 transition-all bg-primary-500/90 rounded-lg hover:bg-primary-600 text-white">
              <i class="fas fa-arrow-left"></i>
              <span class="hidden md:inline">Volver</span>
            </router-link>

            <button @click="logout" class="flex items-center gap-2 px-4 py-2 transition-all bg-red-500/90 rounded-lg hover:bg-red-600 text-white">
              <i class="fas fa-sign-out-alt"></i>
              <span class="hidden md:inline">Cerrar Sesión</span>
            </button>
          </div>
        </div>
      </header>

      <!-- CONTENIDO -->
      <main class="container px-6 py-10 mx-auto text-white">
        <div class="max-w-2xl mx-auto p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
          <div class="flex items-center gap-4 mb-8">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-4xl font-bold text-white">
              {{ authStore.user?.name?.charAt(0).toUpperCase() }}
            </div>
            <div>
              <h2 class="text-3xl font-bold">{{ authStore.user?.name }}</h2>
              <p class="text-gray-300">{{ authStore.user?.email }}</p>
            </div>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-400">Nombre</label>
              <p class="px-4 py-3 bg-white/5 rounded-xl border border-white/10">{{ authStore.user?.name }}</p>
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-400">Correo Electrónico</label>
              <p class="px-4 py-3 bg-white/5 rounded-xl border border-white/10">{{ authStore.user?.email }}</p>
            </div>
            <div>
              <label class="block mb-1 text-sm font-medium text-gray-400">ID de Usuario</label>
              <p class="px-4 py-3 bg-white/5 rounded-xl border border-white/10">{{ authStore.user?.id }}</p>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"

export default {
  name: "PerfilUsuario",
  computed: {
    authStore() {
      return useAuthStore()
    },
    dashboardRoute() {
      return this.authStore.isAdmin ? '/admin/dashboard' : '/dashboard'
    }
  },
  methods: {
    logout() {
      this.authStore.logout()
      this.$router.push("/login")
    }
  }
}
</script>

<style scoped>
.particles {
  background-image: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
  animation: float 20s ease-in-out infinite;
}
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}
</style>
