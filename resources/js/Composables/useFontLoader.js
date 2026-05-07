import { ref, onMounted } from 'vue'

export function useFontLoader() {
  const fontsLoaded = ref(false)
  const fontErrors = ref([])
  
  const loadFont = (fontName, fontUrl, weight = '400') => {
    return new Promise((resolve, reject) => {
      const font = new FontFace(fontName, `url(${fontUrl})`, {
        weight: weight,
        display: 'swap'
      })
      
      font.load().then(() => {
        document.fonts.add(font)
        resolve(font)
      }).catch((error) => {
        console.error(`Failed to load font ${fontName}:`, error)
        fontErrors.value.push({
          font: fontName,
          error: error.message,
          timestamp: new Date().toISOString()
        })
        reject(error)
      })
    })
  }
  
  const loadSystemFonts = () => {
    // Define system font fallbacks
    const systemFonts = [
      { name: 'system-ui', weights: ['400', '500', '600', '700'] },
      { name: '-apple-system', weights: ['400', '500', '600', '700'] },
      { name: 'BlinkMacSystemFont', weights: ['400', '500', '600', '700'] },
      { name: 'Segoe UI', weights: ['400', '500', '600', '700'] },
      { name: 'Roboto', weights: ['400', '500', '600', '700'] },
      { name: 'Arial', weights: ['400', '500', '600', '700'] },
      { name: 'Helvetica', weights: ['400', '500', '600', '700'] },
      { name: 'sans-serif', weights: ['400', '500', '600', '700'] }
    ]
    
    systemFonts.forEach(font => {
      font.weights.forEach(weight => {
        const fontFace = new FontFace(font.name, `local('${font.name}')`, {
          weight: weight,
          display: 'swap'
        })
        
        fontFace.load().then(() => {
          document.fonts.add(fontFace)
        }).catch(() => {
          // System fonts might not be available locally, that's okay
        })
      })
    })
  }
  
  const loadGoogleFonts = async () => {
    const googleFonts = [
      {
        name: 'Inter',
        weights: ['400', '500', '600', '700'],
        baseUrl: 'https://fonts.gstatic.com/s/inter/v13/'
      },
      {
        name: 'Plus Jakarta Sans',
        weights: ['400', '600'],
        baseUrl: 'https://fonts.gstatic.com/s/plusjakartasans/v8/'
      }
    ]
    
    const fontFiles = {
      'Inter': {
        '400': 'UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hiA.woff2',
        '500': 'UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuGfZPfht.woff2',
        '600': 'UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuGKZ5f5h.woff2',
        '700': 'UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLuYZAZ9hiA.woff2'
      },
      'Plus Jakarta Sans': {
        '400': 'LDIoaomQNQcsA88c7O9-2PDPk.woff2',
        '600': 'LDIoaomQNQcsA88c7O9-15MFT.woff2'
      }
    }
    
    const loadPromises = []
    
    googleFonts.forEach(font => {
      font.weights.forEach(weight => {
        const fontFile = fontFiles[font.name]?.[weight]
        if (fontFile) {
          const fontUrl = font.baseUrl + fontFile
          loadPromises.push(loadFont(font.name, fontUrl, weight))
        }
      })
    })
    
    try {
      await Promise.allSettled(loadPromises)
      console.log('Google fonts loading completed')
    } catch (error) {
      console.error('Error loading Google fonts:', error)
    }
  }
  
  const loadFonts = async () => {
    try {
      // First load system fonts (immediate fallback)
      loadSystemFonts()
      
      // Then try to load Google fonts with timeout
      const fontLoadPromise = loadGoogleFonts()
      const timeoutPromise = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('Font loading timeout')), 5000)
      })
      
      await Promise.race([fontLoadPromise, timeoutPromise])
      fontsLoaded.value = true
      
    } catch (error) {
      console.warn('Font loading failed, using system fonts:', error)
      fontsLoaded.value = true // Mark as loaded even if failed
    }
  }
  
  const monitorFontErrors = () => {
    // Monitor for font loading errors in console
    const originalError = console.error
    console.error = (...args) => {
      const message = args.join(' ')
      if (message.includes('Failed to decode downloaded font') || 
          message.includes('OTS parsing error')) {
        fontErrors.value.push({
          error: message,
          timestamp: new Date().toISOString()
        })
      }
      originalError.apply(console, args)
    }
  }
  
  const getFontErrorStats = () => {
    return {
      totalErrors: fontErrors.value.length,
      recentErrors: fontErrors.value.filter(error => {
        const errorTime = new Date(error.timestamp)
        const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000)
        return errorTime > fiveMinutesAgo
      }),
      hasErrors: fontErrors.value.length > 0
    }
  }
  
  onMounted(() => {
    monitorFontErrors()
    loadFonts()
  })
  
  return {
    fontsLoaded,
    fontErrors,
    loadFonts,
    getFontErrorStats
  }
}
