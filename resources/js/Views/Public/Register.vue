<template>
  <div class="relative flex items-center justify-center min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-gray-900">
    <!-- Partículas de fondo -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <!-- Card Register -->
    <div class="relative z-10 w-full max-w-lg p-8 mx-4 transition-all duration-500 transform bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl hover:shadow-yellow-500/20 hover:border-yellow-500/30">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <div class="flex items-center justify-center w-20 h-20 transition-transform duration-500 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg hover:scale-110">
          <span class="text-4xl">📝</span>
        </div>
      </div>

      <h2 class="mb-2 text-3xl font-extrabold text-center text-white uppercase tracking-wide">
        Crear Cuenta
      </h2>
      <p class="mb-6 text-sm text-center text-gray-300">
        Únete a Reporte Ciudadanos
      </p>

      <!-- Botón Google -->
      <div class="mb-6">
        <button 
          class="btn-google flex items-center justify-center w-full px-4 py-3 gap-3 text-sm font-medium transition-all duration-300 bg-white border-0 rounded-xl hover:bg-gray-50 hover:shadow-lg transform hover:-translate-y-0.5"
          @click="googleRegister"
        >
          <img :src="googleLogo" alt="Google" class="w-5 h-5" />
          Registrarse con Google
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

      <!-- Formulario -->
      <form @submit.prevent="handleRegister" class="space-y-4" novalidate>
        <!-- Campo Nombre -->
        <div class="group">
          <label class="block mb-2 text-sm font-medium text-gray-200">
            <i class="mr-2 fas fa-user"></i>Nombre completo
          </label>
          <input
            v-model="name"
            type="text"
            required
            :class="[
              'w-full px-4 py-3 transition-all duration-300 border rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400',
              errors.name ? 'border-red-500 bg-red-500/10' : 'border-white/20 hover:border-white/40'
            ]"
            placeholder="Juan Pérez"
            @input="validateName"
          />
          <p v-if="errors.name" class="mt-2 text-xs text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.name }}
          </p>
        </div>

        <!-- Campo Email -->
        <div class="group">
          <label class="block mb-2 text-sm font-medium text-gray-200">
            <i class="mr-2 fas fa-envelope"></i>Correo electrónico
          </label>
          <input
            v-model="email"
            type="email"
            required
            :class="[
              'w-full px-4 py-3 transition-all duration-300 border rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400',
              errors.email ? 'border-red-500 bg-red-500/10' : 'border-white/20 hover:border-white/40'
            ]"
            placeholder="tu@correo.com"
            @input="validateEmail"
          />
          <p v-if="errors.email" class="mt-2 text-xs text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.email }}
          </p>
        </div>

        <!-- Campo Contraseña -->
        <div class="group">
          <label class="block mb-2 text-sm font-medium text-gray-200">
            <i class="mr-2 fas fa-lock"></i>Contraseña
          </label>
          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              required
              :class="[
                'w-full px-4 py-3 pr-12 transition-all duration-300 border rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400',
                errors.password ? 'border-red-500 bg-red-500/10' : 'border-white/20 hover:border-white/40'
              ]"
              placeholder="••••••••"
              @input="validatePassword"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute text-gray-400 -translate-y-1/2 right-4 top-1/2 hover:text-white transition-colors"
              aria-label="Mostrar/ocultar contraseña"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          
          <!-- Indicador de fuerza -->
          <div v-if="password" class="mt-2">
            <div class="flex h-1.5 gap-1">
              <div
                v-for="strength in 4"
                :key="strength"
                :class="[
                  'flex-1 rounded-full transition-all duration-300',
                  passwordStrength >= strength ? getPasswordColor(strength) : 'bg-gray-600'
                ]"
              ></div>
            </div>
            <p class="mt-1 text-xs" :class="getStrengthTextColor()">
              <i :class="getStrengthIcon()"></i> {{ passwordStrengthText }}
            </p>
          </div>
          
          <p v-if="errors.password" class="mt-2 text-xs text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.password }}
          </p>
        </div>

        <!-- Campo Confirmar Contraseña -->
        <div class="group">
          <label class="block mb-2 text-sm font-medium text-gray-200">
            <i class="mr-2 fas fa-lock"></i>Confirmar contraseña
          </label>
          <div class="relative">
            <input
              v-model="passwordConfirm"
              :type="showPasswordConfirm ? 'text' : 'password'"
              required
              :class="[
                'w-full px-4 py-3 pr-12 transition-all duration-300 border rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none bg-white/5 text-white placeholder-gray-400',
                errors.passwordConfirm ? 'border-red-500 bg-red-500/10' : 'border-white/20 hover:border-white/40'
              ]"
              placeholder="••••••••"
              @input="validatePasswordConfirm"
            />
            <button
              type="button"
              @click="showPasswordConfirm = !showPasswordConfirm"
              class="absolute text-gray-400 -translate-y-1/2 right-4 top-1/2 hover:text-white transition-colors"
              aria-label="Mostrar/ocultar contraseña"
            >
              <i :class="showPasswordConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
          <p v-if="errors.passwordConfirm" class="mt-2 text-xs text-red-400 flex items-center">
            <i class="mr-1 fas fa-exclamation-circle"></i>{{ errors.passwordConfirm }}
          </p>
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

        <!-- Términos y condiciones -->
        <div class="flex items-start">
          <input type="checkbox" id="terms" required class="w-4 h-4 mt-1 transition border-white/30 rounded bg-white/10 text-yellow-500 focus:ring-yellow-500 focus:ring-offset-0" />
          <label for="terms" class="ml-2 text-xs text-gray-300">
            Acepto los <a href="#" class="text-yellow-400 hover:underline">términos y condiciones</a> y la <a href="#" class="text-yellow-400 hover:underline">política de privacidad</a>
          </label>
        </div>

        <!-- Botón Submit -->
        <button
          type="submit"
          :disabled="loading || !isFormValid"
          class="relative w-full py-3 font-bold text-black transition-all duration-300 overflow-hidden rounded-xl bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-yellow-500/50"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="mr-2 fas fa-circle-notch animate-spin"></i>
            Creando cuenta...
          </span>
          <span v-else class="flex items-center justify-center">
            <i class="mr-2 fas fa-user-plus"></i>
            Registrarse
          </span>
        </button>
      </form>

      <!-- Enlaces -->
      <div class="mt-6 space-y-4 text-center">
        <p class="text-sm text-gray-300">
          ¿Ya tienes cuenta?
          <router-link to="/login" class="font-bold text-yellow-400 transition hover:text-yellow-300 hover:underline">
            Inicia sesión
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
  name: 'Register',
  
  data() {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirm: '',
      showPassword: false,
      showPasswordConfirm: false,
      loading: false,
      error: null,
      successMessage: null,
      googleLogo,
      errors: {
        name: null,
        email: null,
        password: null,
        passwordConfirm: null
      }
    }
  },

  computed: {
    isFormValid() {
      return (
        this.name &&
        this.email &&
        this.password &&
        this.passwordConfirm &&
        !this.errors.name &&
        !this.errors.email &&
        !this.errors.password &&
        !this.errors.passwordConfirm
      )
    },

    passwordStrength() {
      let strength = 0
      if (this.password.length >= 8) strength++
      if (this.password.length >= 12) strength++
      if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) strength++
      if (/\d/.test(this.password) && /[^a-zA-Z\d]/.test(this.password)) strength++
      return strength
    },

    passwordStrengthText() {
      switch (this.passwordStrength) {
        case 0:
        case 1:
          return 'Muy débil'
        case 2:
          return 'Regular'
        case 3:
          return 'Fuerte'
        case 4:
          return 'Muy fuerte'
        default:
          return ''
      }
    }
  },

  methods: {
    googleRegister() {
      window.location.href = "/api/auth/google/redirect";
    },
    
    getPasswordColor(strength) {
      const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500']
      return colors[strength - 1] || 'bg-gray-600'
    },

    getStrengthTextColor() {
      if (this.passwordStrength <= 1) return 'text-red-400'
      if (this.passwordStrength === 2) return 'text-yellow-400'
      return 'text-green-400'
    },

    getStrengthIcon() {
      if (this.passwordStrength <= 1) return 'fas fa-times-circle'
      if (this.passwordStrength === 2) return 'fas fa-exclamation-triangle'
      return 'fas fa-check-circle'
    },

    validateName() {
      if (!this.name) {
        this.errors.name = 'El nombre es requerido'
      } else if (this.name.length < 3) {
        this.errors.name = 'Mínimo 3 caracteres'
      } else {
        this.errors.name = null
      }
    },

    validateEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!this.email) {
        this.errors.email = 'El correo es requerido'
      } else if (!emailRegex.test(this.email)) {
        this.errors.email = 'Correo inválido'
      } else {
        this.errors.email = null
      }
    },

    validatePassword() {
      if (!this.password) {
        this.errors.password = 'La contraseña es requerida'
      } else if (this.password.length < 8) {
        this.errors.password = 'Mínimo 8 caracteres'
      } else if (!/[A-Z]/.test(this.password)) {
        this.errors.password = 'Debe tener mayúsculas'
      } else if (!/\d/.test(this.password)) {
        this.errors.password = 'Debe tener números'
      } else {
        this.errors.password = null
      }
      
      if (this.passwordConfirm) {
        this.validatePasswordConfirm()
      }
    },

    validatePasswordConfirm() {
      if (!this.passwordConfirm) {
        this.errors.passwordConfirm = 'Confirma la contraseña'
      } else if (this.password !== this.passwordConfirm) {
        this.errors.passwordConfirm = 'Las contraseñas no coinciden'
      } else {
        this.errors.passwordConfirm = null
      }
    },

    async handleRegister() {
      this.validateName()
      this.validateEmail()
      this.validatePassword()
      this.validatePasswordConfirm()

      if (!this.isFormValid) {
        this.error = 'Por favor, completa el formulario correctamente'
        return
      }

      const authStore = useAuthStore()
      this.loading = true
      this.error = null
      this.successMessage = null

      try {
        this.successMessage = 'Creando tu cuenta...'

        await authStore.register({
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirm
        })

        setTimeout(() => {
          if (authStore.user?.role === 'admin') {
            this.$router.push({ name: 'admin.dashboard' })
          } else {
            this.$router.push({ name: 'user.dashboard' })
          }
        }, 500)
      } catch (error) {
        this.successMessage = null
        if (error.response?.status === 422) {
          const serverErrors = error.response.data.errors || {}
          this.errors.name = serverErrors.name ? serverErrors.name[0] : this.errors.name
          this.errors.email = serverErrors.email ? serverErrors.email[0] : this.errors.email
          this.errors.password = serverErrors.password ? serverErrors.password[0] : this.errors.password
          this.error = 'Corrige los errores del formulario'
        } else if (error.message === 'Network Error') {
          this.error = 'Error de conexión. Verifica tu internet'
        } else {
          this.error = 'Error al crear la cuenta. Intenta nuevamente'
        }
        console.error('Error en registro:', error)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
/* Partículas */
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

/* Glassmorphism */
.bg-white\/10 {
  backdrop-filter: blur(20px);
}

/* Input transitions */
input:focus {
  transition: all 0.3s ease;
}

/* Button effects */
button:not(:disabled):hover {
  transform: translateY(-2px);
}

button:active {
  transform: scale(0.98);
}
</style>
