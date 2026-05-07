<template>
  <nav
    :class="['fixed w-full z-50 transition-all duration-300', { 'shadow-xl bg-white/95 backdrop-blur-md': scrolled, 'bg-transparent': !scrolled } ]"
    role="navigation"
    aria-label="Main navigation"
  >
    <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4 sm:gap-8">
          <img :src="navbarLogo" alt="Site logo" class="h-8 sm:h-10 lg:h-12 transition-transform duration-200 hover:scale-105" />

          <!-- desktop links -->
          <ul class="hidden space-x-1 sm:space-x-2 font-semibold text-gray-700 lg:flex">
            <li><a href="#services" class="px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 min-h-touch min-w-touch flex items-center text-sm">Services</a></li>
            <li><a href="#portfolio" class="px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 min-h-touch min-w-touch flex items-center text-sm">Portfolio</a></li>
            <li><a href="#about" class="px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 min-h-touch min-w-touch flex items-center text-sm">About</a></li>
            <li><a href="#team" class="px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 min-h-touch min-w-touch flex items-center text-sm">Team</a></li>
            <li><a href="#contact" class="px-3 py-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 min-h-touch min-w-touch flex items-center text-sm">Contact</a></li>
          </ul>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
          <!-- auth buttons -->
          <div class="hidden lg:flex items-center gap-2">
            <router-link to="/login" class="px-3 py-2 text-blue-600 hover:text-blue-700 font-medium transition-colors duration-200 text-sm">Login</router-link>
            <router-link to="/register" class="btn-primary text-xs sm:text-sm px-3 sm:px-4">Register</router-link>
          </div>

          <!-- hamburger -->
          <button
            class="text-gray-700 lg:hidden focus:outline-none p-2 sm:p-3 min-w-touch min-h-touch flex items-center justify-center rounded-xl hover:bg-gray-100 transition-colors duration-200"
            @click="toggleMenu"
            aria-label="Toggle menu"
          >
            <i class="fas fa-bars text-lg sm:text-xl"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="menuOpen"
        class="lg:hidden px-4 sm:px-6 py-3 sm:py-4 bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-xl"
        @keydown.escape="menuOpen = false"
      >
        <div class="space-y-1">
          <a href="#services" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">Services</a>
          <a href="#portfolio" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">Portfolio</a>
          <a href="#about" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">About</a>
          <a href="#team" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">Team</a>
          <a href="#contact" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">Contact</a>
          <div class="border-t border-gray-200 pt-3 mt-3">
            <router-link to="/login" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 font-medium text-sm">Login</router-link>
            <router-link to="/register" class="block px-3 py-3 sm:px-4 sm:py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-all duration-200 font-medium text-sm mt-2">Register</router-link>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script>
import navbarLogo from '@/assets/images/navbar-logo.svg'

export default {
  data() {
    return {
      menuOpen: false,
      scrolled: false,
      navbarLogo,
    }
  },
  mounted() {
    window.addEventListener('scroll', this.onScroll)
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.onScroll)
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen
    },
    onScroll() {
      this.scrolled = window.scrollY > 10
      if (this.menuOpen && window.scrollY > 200) {
        this.menuOpen = false
      }
    },
  },
}
</script>

<style scoped>
.navbar-scrolled {
  backdrop-filter: blur(10px);
}
</style>