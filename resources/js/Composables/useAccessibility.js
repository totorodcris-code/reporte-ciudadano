import { ref, onMounted, onUnmounted } from 'vue'

export function useAccessibility() {
  const isHighContrast = ref(false)
  const isReducedMotion = ref(false)
  const fontSize = ref('normal')
  const isKeyboardNavigation = ref(false)
  const announcements = ref([])
  
  // Check for user preferences
  const checkPreferences = () => {
    // High contrast
    if (window.matchMedia('(prefers-contrast: high)').matches) {
      isHighContrast.value = true
    }
    
    // Reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      isReducedMotion.value = true
    }
    
    // Font size preference
    const savedFontSize = localStorage.getItem('fontSize')
    if (savedFontSize) {
      fontSize.value = savedFontSize
    }
  }
  
  // Keyboard navigation detection
  const handleKeyDown = (event) => {
    if (event.key === 'Tab') {
      isKeyboardNavigation.value = true
      document.body.classList.add('keyboard-navigation')
    }
  }
  
  const handleMouseDown = () => {
    isKeyboardNavigation.value = false
    document.body.classList.remove('keyboard-navigation')
  }
  
  // High contrast toggle
  const toggleHighContrast = () => {
    isHighContrast.value = !isHighContrast.value
    document.body.classList.toggle('high-contrast', isHighContrast.value)
    localStorage.setItem('highContrast', isHighContrast.value)
    announce(isHighContrast.value ? 'Alto contraste activado' : 'Alto contraste desactivado')
  }
  
  // Font size controls
  const increaseFontSize = () => {
    const sizes = ['small', 'normal', 'large', 'extra-large']
    const currentIndex = sizes.indexOf(fontSize.value)
    const nextIndex = Math.min(currentIndex + 1, sizes.length - 1)
    fontSize.value = sizes[nextIndex]
    applyFontSize()
    announce(`Tamaño de fuente: ${getFontSizeLabel(fontSize.value)}`)
  }
  
  const decreaseFontSize = () => {
    const sizes = ['small', 'normal', 'large', 'extra-large']
    const currentIndex = sizes.indexOf(fontSize.value)
    const prevIndex = Math.max(currentIndex - 1, 0)
    fontSize.value = sizes[prevIndex]
    applyFontSize()
    announce(`Tamaño de fuente: ${getFontSizeLabel(fontSize.value)}`)
  }
  
  const applyFontSize = () => {
    const root = document.documentElement
    root.classList.remove('font-small', 'font-normal', 'font-large', 'font-extra-large')
    root.classList.add(`font-${fontSize.value}`)
    localStorage.setItem('fontSize', fontSize.value)
  }
  
  const getFontSizeLabel = (size) => {
    const labels = {
      'small': 'Pequeño',
      'normal': 'Normal',
      'large': 'Grande',
      'extra-large': 'Extra Grande'
    }
    return labels[size] || 'Normal'
  }
  
  // Screen reader announcements
  const announce = (message, priority = 'polite') => {
    const announcement = {
      id: Date.now(),
      message,
      priority,
      timestamp: new Date().toISOString()
    }
    
    announcements.value.push(announcement)
    
    // Create live region element
    const liveRegion = document.createElement('div')
    liveRegion.setAttribute('aria-live', priority)
    liveRegion.setAttribute('aria-atomic', 'true')
    liveRegion.className = 'sr-only'
    liveRegion.textContent = message
    
    document.body.appendChild(liveRegion)
    
    // Remove after announcement
    setTimeout(() => {
      document.body.removeChild(liveRegion)
    }, 1000)
  }
  
  // Focus management
  const trapFocus = (element) => {
    const focusableElements = element.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    )
    const firstFocusable = focusableElements[0]
    const lastFocusable = focusableElements[focusableElements.length - 1]
    
    const handleTabKey = (e) => {
      if (e.key === 'Tab') {
        if (e.shiftKey) {
          if (document.activeElement === firstFocusable) {
            lastFocusable.focus()
            e.preventDefault()
          }
        } else {
          if (document.activeElement === lastFocusable) {
            firstFocusable.focus()
            e.preventDefault()
          }
        }
      }
    }
    
    element.addEventListener('keydown', handleTabKey)
    firstFocusable.focus()
    
    return () => {
      element.removeEventListener('keydown', handleTabKey)
    }
  }
  
  // Skip links
  const addSkipLinks = () => {
    const skipLinks = [
      { href: '#main-content', text: 'Saltar al contenido principal' },
      { href: '#navigation', text: 'Saltar a la navegación' },
      { href: '#search', text: 'Saltar a la búsqueda' }
    ]
    
    skipLinks.forEach(link => {
      const a = document.createElement('a')
      a.href = link.href
      a.textContent = link.text
      a.className = 'skip-link'
      document.body.insertBefore(a, document.body.firstChild)
    })
  }
  
  // ARIA live regions
  const createLiveRegion = (id, priority = 'polite') => {
    const region = document.createElement('div')
    region.id = id
    region.setAttribute('aria-live', priority)
    region.setAttribute('aria-atomic', 'true')
    region.className = 'sr-only'
    document.body.appendChild(region)
    
    return region
  }
  
  // Color blindness support
  const checkColorBlindness = () => {
    const savedPreference = localStorage.getItem('colorBlindnessMode')
    if (savedPreference) {
      document.body.classList.add(`colorblind-${savedPreference}`)
    }
  }
  
  const setColorBlindnessMode = (mode) => {
    const modes = ['normal', 'protanopia', 'deuteranopia', 'tritanopia']
    modes.forEach(m => document.body.classList.remove(`colorblind-${m}`))
    
    if (mode !== 'normal') {
      document.body.classList.add(`colorblind-${mode}`)
    }
    
    localStorage.setItem('colorBlindnessMode', mode)
    announce(`Modo de daltonismo: ${getColorBlindnessLabel(mode)}`)
  }
  
  const getColorBlindnessLabel = (mode) => {
    const labels = {
      'normal': 'Normal',
      'protanopia': 'Protanopia',
      'deuteranopia': 'Deuteranopia',
      'tritanopia': 'Tritanopia'
    }
    return labels[mode] || 'Normal'
  }
  
  // Screen reader optimization
  const optimizeForScreenReader = () => {
    // Add ARIA labels to interactive elements
    const buttons = document.querySelectorAll('button:not([aria-label])')
    buttons.forEach(button => {
      if (!button.textContent.trim()) {
        button.setAttribute('aria-label', 'Botón')
      }
    })
    
    // Add role to landmark elements
    const landmarks = {
      'header': 'banner',
      'nav': 'navigation',
      'main': 'main',
      'aside': 'complementary',
      'footer': 'contentinfo'
    }
    
    Object.entries(landmarks).forEach(([tag, role]) => {
      const elements = document.querySelectorAll(tag)
      elements.forEach(element => {
        if (!element.getAttribute('role')) {
          element.setAttribute('role', role)
        }
      })
    })
  }
  
  // Initialize accessibility features
  const init = () => {
    checkPreferences()
    checkColorBlindness()
    addSkipLinks()
    optimizeForScreenReader()
    applyFontSize()
    
    // Load saved preferences
    const savedHighContrast = localStorage.getItem('highContrast')
    if (savedHighContrast === 'true') {
      isHighContrast.value = true
      document.body.classList.add('high-contrast')
    }
  }
  
  onMounted(() => {
    init()
    
    // Event listeners
    window.addEventListener('keydown', handleKeyDown)
    window.addEventListener('mousedown', handleMouseDown)
    
    // Listen for preference changes
    window.matchMedia('(prefers-contrast: high)').addEventListener('change', (e) => {
      isHighContrast.value = e.matches
      document.body.classList.toggle('high-contrast', e.matches)
    })
    
    window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
      isReducedMotion.value = e.matches
      document.body.classList.toggle('reduced-motion', e.matches)
    })
  })
  
  onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown)
    window.removeEventListener('mousedown', handleMouseDown)
  })
  
  return {
    isHighContrast,
    isReducedMotion,
    fontSize,
    isKeyboardNavigation,
    announcements,
    toggleHighContrast,
    increaseFontSize,
    decreaseFontSize,
    announce,
    trapFocus,
    createLiveRegion,
    setColorBlindnessMode,
    init
  }
}
