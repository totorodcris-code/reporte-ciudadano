import { ref, onMounted, onUnmounted } from 'vue'

export function useImageOptimization() {
  const isIntersecting = ref(false)
  const hasLoaded = ref(false)
  const hasError = ref(false)
  
  const optimizeImageUrl = (url, options = {}) => {
    if (!url) return url
    
    const {
      width,
      height,
      quality = 80,
      format = 'webp'
    } = options
    
    // If it's already an optimized URL or external, return as-is
    if (url.includes('?') || url.startsWith('http')) {
      return url
    }
    
    // Build optimized URL parameters
    const params = new URLSearchParams()
    if (width) params.set('w', width)
    if (height) params.set('h', height)
    params.set('q', quality)
    params.set('f', format)
    
    return `${url}?${params.toString()}`
  }
  
  const generateSrcSet = (url, sizes = []) => {
    if (!url || sizes.length === 0) return ''
    
    return sizes
      .map(size => {
        const optimizedUrl = optimizeImageUrl(url, { 
          width: size.width,
          height: size.height 
        })
        return `${optimizedUrl} ${size.width}w`
      })
      .join(', ')
  }
  
  const generateResponsiveSizes = (sizes = []) => {
    if (sizes.length === 0) return '100vw'
    
    return sizes
      .map((size, index) => {
        if (index === sizes.length - 1) {
          return `${size.width}px`
        }
        return `(max-width: ${size.width}px) ${size.width}px`
      })
      .join(', ')
  }
  
  const preloadImage = (src) => {
    return new Promise((resolve, reject) => {
      const img = new Image()
      img.onload = () => resolve(img)
      img.onerror = reject
      img.src = src
    })
  }
  
  const lazyLoadImage = async (element, src, options = {}) => {
    if (!element || !src) return
    
    const optimizedSrc = optimizeImageUrl(src, options)
    
    try {
      // Preload the image
      await preloadImage(optimizedSrc)
      
      // Set the image source
      if (element.tagName === 'IMG') {
        element.src = optimizedSrc
      } else {
        element.style.backgroundImage = `url(${optimizedSrc})`
      }
      
      hasLoaded.value = true
    } catch (error) {
      hasError.value = true
      console.error('Failed to load image:', error)
    }
  }
  
  const createIntersectionObserver = (element, callback) => {
    if (!window.IntersectionObserver) {
      // Fallback for older browsers
      callback()
      return null
    }
    
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            isIntersecting.value = true
            callback()
            observer.unobserve(element)
          }
        })
      },
      {
        rootMargin: '50px 0px',
        threshold: 0.01
      }
    )
    
    observer.observe(element)
    return observer
  }
  
  const applyLazyLoading = (element, src, options = {}) => {
    if (!element || !src) return
    
    let observer = null
    
    const loadImage = () => {
      lazyLoadImage(element, src, options)
      if (observer) {
        observer.disconnect()
      }
    }
    
    // Use Intersection Observer for lazy loading
    observer = createIntersectionObserver(element, loadImage)
    
    // Fallback: load image immediately if Intersection Observer is not supported
    if (!observer) {
      loadImage()
    }
  }
  
  const compressImage = async (file, options = {}) => {
    const {
      maxWidth = 1920,
      maxHeight = 1080,
      quality = 0.8,
      outputFormat = 'image/jpeg'
    } = options
    
    return new Promise((resolve) => {
      const canvas = document.createElement('canvas')
      const ctx = canvas.getContext('2d')
      const img = new Image()
      
      img.onload = () => {
        // Calculate new dimensions
        let { width, height } = img
        
        if (width > maxWidth) {
          height = (maxWidth / width) * height
          width = maxWidth
        }
        
        if (height > maxHeight) {
          width = (maxHeight / height) * width
          height = maxHeight
        }
        
        canvas.width = width
        canvas.height = height
        
        // Draw and compress image
        ctx.drawImage(img, 0, 0, width, height)
        
        canvas.toBlob(
          (blob) => {
            resolve(new File([blob], file.name, {
              type: outputFormat,
              lastModified: Date.now()
            }))
          },
          outputFormat,
          quality
        )
      }
      
      img.src = URL.createObjectURL(file)
    })
  }
  
  onUnmounted(() => {
    // Cleanup observers if needed
  })
  
  return {
    isIntersecting,
    hasLoaded,
    hasError,
    optimizeImageUrl,
    generateSrcSet,
    generateResponsiveSizes,
    preloadImage,
    lazyLoadImage,
    applyLazyLoading,
    compressImage
  }
}
