<template>
  <button
    @click="themeStore.toggle()"
    :aria-label="themeStore.isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    :title="themeStore.isDark ? 'Modo claro' : 'Modo oscuro'"
    class="relative flex items-center justify-center w-12 h-12 transition-all duration-300 rounded-full min-w-touch min-h-touch focus-visible-ring"
    :class="[
      themeStore.isDark
        ? 'bg-gray-700 text-yellow-400 hover:bg-gray-600'
        : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
    ]"
  >
    <transition name="fade" mode="out-in">
      <i v-if="themeStore.isDark" key="moon" class="text-lg fas fa-moon"></i>
      <i v-else key="sun" class="text-lg fas fa-sun"></i>
    </transition>
  </button>
</template>

<script>
import { useThemeStore } from '@/stores/theme'

export default {
  name: 'ThemeToggle',
  setup() {
    const themeStore = useThemeStore()
    return { themeStore }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.fade-enter-from {
  opacity: 0;
  transform: rotate(-90deg) scale(0.5);
}
.fade-leave-to {
  opacity: 0;
  transform: rotate(90deg) scale(0.5);
}
</style>