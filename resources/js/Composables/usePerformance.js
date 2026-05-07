import { ref, onMounted, onUnmounted } from 'vue'

export function usePerformance() {
  const metrics = ref({
    navigationStart: 0,
    navigationEnd: 0,
    loadEventEnd: 0,
    domContentLoaded: 0,
    firstContentfulPaint: 0,
    largestContentfulPaint: 0,
    firstInputDelay: 0,
    cumulativeLayoutShift: 0
  })
  
  const isSupported = ref(!!window.performance)
  const observer = ref(null)
  
  const measurePageLoad = () => {
    if (!isSupported.value) return
    
    const navigation = performance.getEntriesByType('navigation')[0]
    const paint = performance.getEntriesByType('paint')
    const vitals = performance.getEntriesByType('largest-contentful-paint')
    
    metrics.value = {
      navigationStart: navigation?.navigationStart || 0,
      navigationEnd: navigation?.loadEventEnd || 0,
      loadEventEnd: navigation?.loadEventEnd || 0,
      domContentLoaded: navigation?.domContentLoadedEventEnd || 0,
      firstContentfulPaint: paint.find(p => p.name === 'first-contentful-paint')?.startTime || 0,
      largestContentfulPaint: vitals[0]?.startTime || 0,
      firstInputDelay: 0, // Would need separate measurement
      cumulativeLayoutShift: 0 // Would need separate measurement
    }
  }
  
  const measureComponentRender = (componentName) => {
    if (!isSupported.value) return () => {}
    
    const startTime = performance.now()
    
    return () => {
      const endTime = performance.now()
      const renderTime = endTime - startTime
      
      console.log(`Component ${componentName} render time: ${renderTime.toFixed(2)}ms`)
      
      // Send to analytics if needed
      if (window.gtag) {
        window.gtag('event', 'component_render_time', {
          component_name: componentName,
          render_time: renderTime
        })
      }
      
      return renderTime
    }
  }
  
  const startTiming = (label) => {
    if (!isSupported.value) return
    performance.mark(`${label}-start`)
  }
  
  const endTiming = (label) => {
    if (!isSupported.value) return
    performance.mark(`${label}-end`)
    performance.measure(label, `${label}-start`, `${label}-end`)
    
    const measure = performance.getEntriesByName(label, 'measure')[0]
    return measure?.duration || 0
  }
  
  const measureNetworkRequest = async (url, requestFn) => {
    if (!isSupported.value) {
      return await requestFn()
    }
    
    const startTime = performance.now()
    startTiming(`network-${url}`)
    
    try {
      const response = await requestFn()
      const endTime = performance.now()
      const duration = endTime - startTime
      
      endTiming(`network-${url}`)
      
      console.log(`Network request to ${url}: ${duration.toFixed(2)}ms`)
      
      // Track slow requests
      if (duration > 2000) {
        console.warn(`Slow network request detected: ${url} took ${duration.toFixed(2)}ms`)
      }
      
      return response
    } catch (error) {
      const endTime = performance.now()
      const duration = endTime - startTime
      
      endTiming(`network-${url}`)
      console.error(`Network request to ${url} failed after ${duration.toFixed(2)}ms:`, error)
      throw error
    }
  }
  
  const observeLayoutShift = () => {
    if (!window.PerformanceObserver) return
    
    observer.value = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (!entry.hadRecentInput) {
          metrics.value.cumulativeLayoutShift += entry.value
        }
      }
    })
    
    observer.value.observe({ entryTypes: ['layout-shift'] })
  }
  
  const observeLargestContentfulPaint = () => {
    if (!window.PerformanceObserver) return
    
    const lcpObserver = new PerformanceObserver((list) => {
      const entries = list.getEntries()
      const lastEntry = entries[entries.length - 1]
      
      if (lastEntry) {
        metrics.value.largestContentfulPaint = lastEntry.startTime
      }
    })
    
    lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] })
  }
  
  const observeFirstInput = () => {
    if (!window.PerformanceObserver) return
    
    const fidObserver = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (entry.name === 'first-input') {
          metrics.value.firstInputDelay = entry.processingStart - entry.startTime
          break
        }
      }
    })
    
    fidObserver.observe({ entryTypes: ['first-input'] })
  }
  
  const getPerformanceScore = () => {
    const scores = {
      fcp: getFCPScore(metrics.value.firstContentfulPaint),
      lcp: getLCPScore(metrics.value.largestContentfulPaint),
      cls: getCLSScore(metrics.value.cumulativeLayoutShift),
      fid: getFIDScore(metrics.value.firstInputDelay)
    }
    
    const overall = Object.values(scores).reduce((sum, score) => sum + score, 0) / Object.keys(scores).length
    
    return {
      ...scores,
      overall,
      grade: getPerformanceGrade(overall)
    }
  }
  
  const getFCPScore = (fcp) => {
    if (fcp < 1800) return 100
    if (fcp < 3000) return 67
    return 33
  }
  
  const getLCPScore = (lcp) => {
    if (lcp < 2500) return 100
    if (lcp < 4000) return 67
    return 33
  }
  
  const getCLSScore = (cls) => {
    if (cls < 0.1) return 100
    if (cls < 0.25) return 67
    return 33
  }
  
  const getFIDScore = (fid) => {
    if (fid < 100) return 100
    if (fid < 300) return 67
    return 33
  }
  
  const getPerformanceGrade = (score) => {
    if (score >= 90) return 'A'
    if (score >= 80) return 'B'
    if (score >= 70) return 'C'
    if (score >= 60) return 'D'
    return 'F'
  }
  
  const logPerformanceMetrics = () => {
    const score = getPerformanceScore()
    
    console.group('Performance Metrics')
    console.log('Navigation Time:', metrics.value.navigationEnd - metrics.value.navigationStart, 'ms')
    console.log('DOM Content Loaded:', metrics.value.domContentLoaded - metrics.value.navigationStart, 'ms')
    console.log('First Contentful Paint:', metrics.value.firstContentfulPaint, 'ms')
    console.log('Largest Contentful Paint:', metrics.value.largestContentfulPaint, 'ms')
    console.log('Cumulative Layout Shift:', metrics.value.cumulativeLayoutShift)
    console.log('First Input Delay:', metrics.value.firstInputDelay, 'ms')
    console.log('Performance Score:', score)
    console.groupEnd()
    
    // Send to analytics
    if (window.gtag) {
      window.gtag('event', 'performance_metrics', {
        custom_map: {
          dimension1: score.fcp,
          dimension2: score.lcp,
          dimension3: score.cls,
          dimension4: score.fid,
          dimension5: score.overall,
          dimension6: score.grade
        }
      })
    }
  }
  
  onMounted(() => {
    if (isSupported.value) {
      // Measure initial page load
      if (document.readyState === 'complete') {
        measurePageLoad()
      } else {
        window.addEventListener('load', measurePageLoad)
      }
      
      // Start observing performance metrics
      observeLayoutShift()
      observeLargestContentfulPaint()
      observeFirstInput()
      
      // Log metrics after page is fully loaded
      setTimeout(logPerformanceMetrics, 1000)
    }
  })
  
  onUnmounted(() => {
    if (observer.value) {
      observer.value.disconnect()
    }
    window.removeEventListener('load', measurePageLoad)
  })
  
  return {
    metrics,
    isSupported,
    measureComponentRender,
    startTiming,
    endTiming,
    measureNetworkRequest,
    getPerformanceScore,
    logPerformanceMetrics
  }
}
