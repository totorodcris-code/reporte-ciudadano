<template>
  <div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Accesibilidad</h3>
      <button
        @click="resetAllSettings"
        class="text-sm text-gray-600 hover:text-gray-800 transition-colors duration-200"
      >
        Restablecer todo
      </button>
    </div>
    
    <div class="space-y-6">
      <!-- High Contrast -->
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-md font-medium text-gray-900">Alto Contraste</h4>
          <p class="text-sm text-gray-600">Mejora la visibilidad con colores de alto contraste</p>
        </div>
        <button
          @click="toggleHighContrast"
          class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
          :class="isHighContrast ? 'bg-blue-600' : 'bg-gray-200'"
        >
          <span
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"
            :class="isHighContrast ? 'translate-x-6' : 'translate-x-1'"
          ></span>
        </button>
      </div>
      
      <!-- Font Size -->
      <div>
        <h4 class="text-md font-medium text-gray-900 mb-3">Tamaño de Fuente</h4>
        <div class="flex items-center gap-3">
          <button
            @click="decreaseFontSize"
            class="p-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg transition-colors duration-200"
            :disabled="fontSize === 'small'"
          >
            <i class="fas fa-minus"></i>
          </button>
          <div class="flex-1 text-center">
            <span class="text-sm font-medium text-gray-900">{{ getFontSizeLabel(fontSize) }}</span>
          </div>
          <button
            @click="increaseFontSize"
            class="p-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg transition-colors duration-200"
            :disabled="fontSize === 'extra-large'"
          >
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <!-- Color Blindness Mode -->
      <div>
        <h4 class="text-md font-medium text-gray-900 mb-3">Modo de Daltonismo</h4>
        <select
          v-model="selectedColorBlindnessMode"
          @change="setColorBlindnessMode(selectedColorBlindnessMode)"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="normal">Normal</option>
          <option value="protanopia">Protanopia (Rojo-Verde)</option>
          <option value="deuteranopia">Deuteranopia (Rojo-Verde)</option>
          <option value="tritanopia">Tritanopia (Azul-Amarillo)</option>
        </select>
      </div>
      
      <!-- Reduced Motion -->
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-md font-medium text-gray-900">Movimiento Reducido</h4>
          <p class="text-sm text-gray-600">Reduce las animaciones y transiciones</p>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600">{{ isReducedMotion ? 'Activado' : 'Desactivado' }}</span>
          <div
            class="w-3 h-3 rounded-full"
            :class="isReducedMotion ? 'bg-green-500' : 'bg-gray-300'"
          ></div>
        </div>
      </div>
      
      <!-- Keyboard Navigation -->
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-md font-medium text-gray-900">Navegación por Teclado</h4>
          <p class="text-sm text-gray-600">{{ isKeyboardNavigation ? 'Usando teclado' : 'Usando mouse' }}</p>
        </div>
        <div class="flex items-center gap-2">
          <i :class="isKeyboardNavigation ? 'fas fa-keyboard text-blue-600' : 'fas fa-mouse text-gray-400'"></i>
        </div>
      </div>
      
      <!-- Screen Reader Support -->
      <div>
        <h4 class="text-md font-medium text-gray-900 mb-3">Soporte de Lector de Pantalla</h4>
        <div class="space-y-2">
          <button
            @click="testScreenReader"
            class="w-full px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-volume-up mr-2"></i>
            Probar Anuncio de Lector de Pantalla
          </button>
          <button
            @click="announceCurrentSettings"
            class="w-full px-4 py-2 text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-info-circle mr-2"></i>
            Anunciar Configuración Actual
          </button>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div>
        <h4 class="text-md font-medium text-gray-900 mb-3">Acciones Rápidas</h4>
        <div class="grid grid-cols-2 gap-3">
          <button
            @click="focusMainContent"
            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-arrow-down mr-2"></i>
            Ir al Contenido
          </button>
          <button
            @click="focusNavigation"
            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-bars mr-2"></i>
            Ir a Navegación
          </button>
          <button
            @click="skipToSearch"
            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-search mr-2"></i>
            Ir a Búsqueda
          </button>
          <button
            @click="toggleAccessibilityHelp"
            class="px-4 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
          >
            <i class="fas fa-question-circle mr-2"></i>
            Ayuda
          </button>
        </div>
      </div>
      
      <!-- Accessibility Help Modal -->
      <div
        v-if="showAccessibilityHelp"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click="showAccessibilityHelp = false"
      >
        <div
          class="bg-white rounded-xl shadow-lg p-6 max-w-2xl max-h-[80vh] overflow-y-auto"
          @click.stop
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Atajos de Teclado</h3>
            <button
              @click="showAccessibilityHelp = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Navegación</h4>
                <ul class="space-y-1 text-sm text-gray-600">
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Tab</kbd> Siguiente elemento</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Shift + Tab</kbd> Elemento anterior</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Enter</kbd> Activar botón/enlace</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Space</kbd> Activar checkbox</li>
                </ul>
              </div>
              
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Accesibilidad</h4>
                <ul class="space-y-1 text-sm text-gray-600">
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Alt + H</kbd> Panel de accesibilidad</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Alt + C</kbd> Alto contraste</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Alt + +</kbd> Aumentar fuente</li>
                  <li><kbd class="px-2 py-1 bg-gray-100 rounded">Alt + -</kbd> Disminuir fuente</li>
                </ul>
              </div>
            </div>
            
            <div>
              <h4 class="font-medium text-gray-900 mb-2">Lector de Pantalla</h4>
              <p class="text-sm text-gray-600">
                Esta aplicación está optimizada para lectores de pantalla con etiquetas ARIA, 
                regiones en vivo y anuncios automáticos de cambios importantes.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAccessibility } from '@/Composables/useAccessibility'

export default {
  name: 'AccessibilityPanel',
  setup() {
    const {
      isHighContrast,
      isReducedMotion,
      fontSize,
      isKeyboardNavigation,
      toggleHighContrast,
      increaseFontSize,
      decreaseFontSize,
      announce,
      setColorBlindnessMode
    } = useAccessibility()
    
    const selectedColorBlindnessMode = ref('normal')
    const showAccessibilityHelp = ref(false)
    
    const getFontSizeLabel = (size) => {
      const labels = {
        'small': 'Pequeño',
        'normal': 'Normal',
        'large': 'Grande',
        'extra-large': 'Extra Grande'
      }
      return labels[size] || 'Normal'
    }
    
    const testScreenReader = () => {
      announce('Este es un mensaje de prueba para el lector de pantalla')
    }
    
    const announceCurrentSettings = () => {
      const settings = [
        `Alto contraste: ${isHighContrast.value ? 'activado' : 'desactivado'}`,
        `Tamaño de fuente: ${getFontSizeLabel(fontSize.value)}`,
        `Movimiento reducido: ${isReducedMotion.value ? 'activado' : 'desactivado'}`,
        `Modo de navegación: ${isKeyboardNavigation.value ? 'teclado' : 'mouse'}`
      ]
      announce('Configuración de accesibilidad actual: ' + settings.join(', '))
    }
    
    const focusMainContent = () => {
      const mainContent = document.querySelector('#main-content') || document.querySelector('main')
      if (mainContent) {
        mainContent.focus()
        mainContent.scrollIntoView({ behavior: 'smooth' })
      }
    }
    
    const focusNavigation = () => {
      const navigation = document.querySelector('nav') || document.querySelector('[role="navigation"]')
      if (navigation) {
        navigation.focus()
        navigation.scrollIntoView({ behavior: 'smooth' })
      }
    }
    
    const skipToSearch = () => {
      const search = document.querySelector('#search') || document.querySelector('input[type="search"]')
      if (search) {
        search.focus()
        search.scrollIntoView({ behavior: 'smooth' })
      }
    }
    
    const toggleAccessibilityHelp = () => {
      showAccessibilityHelp.value = !showAccessibilityHelp.value
    }
    
    const resetAllSettings = () => {
      // Reset to defaults
      if (isHighContrast.value) toggleHighContrast()
      
      if (fontSize.value !== 'normal') {
        while (fontSize.value !== 'normal') {
          decreaseFontSize()
        }
      }
      
      selectedColorBlindnessMode.value = 'normal'
      setColorBlindnessMode('normal')
      
      announce('Configuración de accesibilidad restablecida')
    }
    
    // Keyboard shortcuts
    const handleKeyDown = (event) => {
      if (event.altKey) {
        switch (event.key) {
          case 'h':
          case 'H':
            event.preventDefault()
            // Focus accessibility panel
            break
          case 'c':
          case 'C':
            event.preventDefault()
            toggleHighContrast()
            break
          case '+':
          case '=':
            event.preventDefault()
            increaseFontSize()
            break
          case '-':
          case '_':
            event.preventDefault()
            decreaseFontSize()
            break
        }
      }
    }
    
    onMounted(() => {
      window.addEventListener('keydown', handleKeyDown)
    })
    
    onUnmounted(() => {
      window.removeEventListener('keydown', handleKeyDown)
    })
    
    return {
      isHighContrast,
      isReducedMotion,
      fontSize,
      isKeyboardNavigation,
      selectedColorBlindnessMode,
      showAccessibilityHelp,
      getFontSizeLabel,
      toggleHighContrast,
      increaseFontSize,
      decreaseFontSize,
      setColorBlindnessMode,
      testScreenReader,
      announceCurrentSettings,
      focusMainContent,
      focusNavigation,
      skipToSearch,
      toggleAccessibilityHelp,
      resetAllSettings
    }
  }
}
</script>

<style scoped>
kbd {
  font-family: monospace;
  font-size: 0.875rem;
  line-height: 1.25rem;
  font-weight: 500;
}

/* Accessibility styles */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.skip-link {
  position: absolute;
  top: -40px;
  left: 6px;
  background: #000;
  color: #fff;
  padding: 8px;
  text-decoration: none;
  border-radius: 4px;
  z-index: 1000;
}

.skip-link:focus {
  top: 6px;
}

.high-contrast {
  filter: contrast(1.5);
}

.high-contrast * {
  border-color: #000 !important;
  color: #000 !important;
  background-color: #fff !important;
}

.reduced-motion * {
  animation-duration: 0.01ms !important;
  animation-iteration-count: 1 !important;
  transition-duration: 0.01ms !important;
}

.keyboard-navigation *:focus {
  outline: 2px solid #005fcc;
  outline-offset: 2px;
}

/* Font size classes */
.font-small {
  font-size: 14px;
}

.font-normal {
  font-size: 16px;
}

.font-large {
  font-size: 18px;
}

.font-extra-large {
  font-size: 20px;
}

/* Color blindness modes */
.colorblind-protanopia {
  filter: url(#protanopia-filter);
}

.colorblind-deuteranopia {
  filter: url(#deuteranopia-filter);
}

.colorblind-tritanopia {
  filter: url(#tritanopia-filter);
}
</style>
