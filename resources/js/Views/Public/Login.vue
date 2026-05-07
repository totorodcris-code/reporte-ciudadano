<template>
  <div class="relative flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900">
    <!-- Partículas de fondo animadas -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <!-- Card Login -->
    <div class="relative z-10 w-full max-w-md p-8 mx-4 transition-all duration-500 transform bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl hover:shadow-yellow-500/20 hover:border-yellow-500/30">
      <!-- Logo o Icono -->
      <div class="flex justify-center mb-6">
        <div class="flex items-center justify-center w-20 h-20 transition-transform duration-500 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg hover:scale-110">
          <span class="text-4xl">🏛️</span>
        </div>
      </div>

      <h2 class="mb-2 text-3xl font-extrabold text-center text-white uppercase tracking-wide">
        Bienvenido
      </h2>
      <p class="mb-8 text-sm text-center text-gray-300">
        Inicia sesión en Reporte Ciudadanos
      </p>

      <!-- Botón social Google -->
      <div class="mb-6">
        <button
          class="btn-google flex items-center justify-center w-full px-4 py-4 gap-3 text-base font-semibold text-gray-700 transition-all duration-300 bg-white border-0 rounded-xl hover:bg-gray-50 hover:shadow-lg transform hover:-translate-y-0.5 min-h-touch"
          style="font-size: 1.125rem;"
          @click="googleLogin"
        >
          <img :src="googleLogo" alt="Google" class="w-6 h-6" />
          Continuar con Google
        </button>
      </div>

      <!-- Divisor -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-white/20"></div>
        </div>
        <div class="relative flex justify-center text-xs">
          <span class="px-4 text-gray-400 bg-transparent backdrop-blur-sm">O usa tu correo</span>
        </div>
      </div>

      <!-- formulario tradicional -->
      <form @submit.prevent="handleLogin" class="space-y-5" novalidate>
        <!-- Campo Email -->
        <div class="group">
          <label class="block mb-2 text-lg font-semibold text-gray-200">
            <i class="mr-2 fas fa-envelope"></i>Correo electrónico
          </label>
          <div class="relative">
            <input
              v-model="email"
              type="email"
              required
              autocomplete="off"
              :class="[
                'w-full px-5 py-4 pl-12 transition-all duration-300 border-2 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400 text-accessible-base',
                errors.email ? 'border-red-500 bg-red-500/10' : 'border-white/30 hover:border-white/50'
              ]"
              style="min-height: 52px; font-size: 1.125rem;"
              placeholder="tu@correo.com"
              @input="validateEmail"
            />
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
              <i class="fas fa-at"></i>
            </div>
          </div>
          <p v-if="errors.email" class="mt-2 text-base text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.email }}
          </p>
        </div>

        <!-- Campo Contraseña -->
        <div class="group">
          <label class="block mb-2 text-lg font-semibold text-gray-200">
            <i class="mr-2 fas fa-lock"></i>Contraseña
          </label>
          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="new-password"
              :class="[
                'w-full px-5 py-4 pl-12 pr-12 transition-all duration-300 border-2 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400 text-accessible-base',
                errors.password ? 'border-red-500 bg-red-500/10' : 'border-white/30 hover:border-white/50'
              ]"
              style="min-height: 52px; font-size: 1.125rem;"
              placeholder="••••••••"
            />
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
              <i class="fas fa-key"></i>
            </div>
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute text-gray-400 -translate-y-1/2 right-4 top-1/2 hover:text-white transition-colors min-w-touch min-h-touch flex items-center justify-center"
              aria-label="Mostrar/ocultar contraseña"
              style="font-size: 1.25rem;"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p v-if="errors.password" class="mt-2 text-base text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.password }}
          </p>
        </div>

        <!-- Recordarme -->
        <div class="flex items-center justify-between">
          <label class="flex items-center cursor-pointer group">
            <input type="checkbox" class="w-5 h-5 transition border-white/30 rounded bg-white/10 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-0" />
            <span class="ml-3 text-accessible-base text-gray-300 group-hover:text-white">Recordarme</span>
          </label>
          <a href="#" class="text-accessible-base text-yellow-400 transition hover:text-yellow-300 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>

        <!-- Mensaje de estado -->
        <transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
          <div
            v-if="successMessage"
            class="p-4 text-sm text-green-300 border border-green-500/30 rounded-xl bg-green-500/10 backdrop-blur-sm flex items-center"
          >
            <i class="mr-2 fas fa-check-circle"></i> {{ successMessage }}
          </div>
        </transition>

        <transition enter-active-class="transition duration-300" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
          <div
            v-if="error"
            class="p-4 text-sm text-red-300 border border-red-500/30 rounded-xl bg-red-500/10 backdrop-blur-sm flex items-center"
          >
            <i class="mr-2 fas fa-exclamation-triangle"></i> {{ error }}
          </div>
        </transition>

        <!-- Botón Submit -->
        <button
          type="submit"
          :disabled="loading || !isFormValid"
          class="relative w-full py-4 font-bold text-black transition-all duration-300 overflow-hidden rounded-xl bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-yellow-500/50 min-h-touch text-accessible-lg"
          style="font-size: 1.25rem;"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="mr-3 fas fa-circle-notch animate-spin"></i>
            Iniciando sesión...
          </span>
          <span v-else class="flex items-center justify-center">
            <i class="mr-3 fas fa-sign-in-alt"></i>
            Iniciar Sesión
          </span>
        </button>
      </form>

      <!-- Enlaces de navegación -->
      <div class="mt-8 space-y-4 text-center">
        <p class="text-sm text-gray-300">
          ¿No tienes cuenta?
          <router-link to="/register" class="font-bold text-yellow-400 transition hover:text-yellow-300 hover:underline">
            Regístrate gratis
          </router-link>
        </p>
        <router-link
          to="/"
          class="inline-flex items-center text-sm text-gray-400 transition hover:text-white group"
        >
          <i class="mr-2 transition-transform group-hover:-translate-x-1 fas fa-arrow-left"></i>
          Volver al inicio
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"
import googleLogo from '@/assets/images/logos/google.svg'

export default {
  name: "Login",

  data() {
    return {
      email: "",
      password: "",
      showPassword: false,
      loading: false,
      error: null,
      successMessage: null,
      rememberMe: false,
      errors: {
        email: null,
        password: null
      },
      googleLogo
    }
  },

  computed: {
    isFormValid() {
      return this.email && this.password && !this.errors.email && !this.errors.password
    }
  },

  methods: {
    validateEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!this.email) {
        this.errors.email = "El correo es requerido"
      } else if (!emailRegex.test(this.email)) {
        this.errors.email = "Ingresa un correo válido"
      } else {
        this.errors.email = null
      }
    },

    googleLogin() {
      window.location.href = "/api/auth/google/redirect";
    },

    async handleLogin() {
      this.validateEmail()
      
      if (!this.isFormValid) {
        this.error = "Por favor, completa el formulario correctamente"
        return
      }

      const authStore = useAuthStore()
      this.loading = true
      this.error = null
      this.successMessage = null

      try {
        this.successMessage = "Verificando credenciales..."
        
        await authStore.login({
          email: this.email,
          password: this.password,
          remember: this.rememberMe
        })

        setTimeout(() => {
          if (authStore.user?.role === "admin") {
            this.$router.push({ name: "admin.dashboard" })
          } else {
            this.$router.push({ name: "user.dashboard" })
          }
        }, 500)

      } catch (err) {
        this.successMessage = null
        if (err.response?.status === 401) {
          this.error = "❌ Correo o contraseña incorrectos"
        } else if (err.response?.status === 429) {
          this.error = "⏱️ Demasiados intentos. Intenta más tarde"
        } else if (err.message === "Network Error") {
          this.error = "🌐 Error de conexión. Verifica tu internet"
        } else {
          this.error = "Error al iniciar sesión. Intenta de nuevo"
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
/* Partículas animadas */
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

form {
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Glassmorphism effect */
.bg-white\/10 {
  backdrop-filter: blur(20px);
}

/* Input transitions */
input:focus {
  transition: all 0.3s ease;
}

/* Button hover effect */
button:not(:disabled):hover {
  transform: translateY(-2px);
}

button:active {
  transform: scale(0.98);
}
</style>
