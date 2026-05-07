import { ref, onMounted, onUnmounted } from 'vue'

export function useTouch(element) {
  const startX = ref(0)
  const startY = ref(0)
  const endX = ref(0)
  const endY = ref(0)
  const isSwiping = ref(false)
  
  const touchStart = (e) => {
    startX.value = e.touches[0].clientX
    startY.value = e.touches[0].clientY
    isSwiping.value = true
  }
  
  const touchMove = (e) => {
    if (!isSwiping.value) return
    
    endX.value = e.touches[0].clientX
    endY.value = e.touches[0].clientY
  }
  
  const touchEnd = (e) => {
    if (!isSwiping.value) return
    
    const deltaX = endX.value - startX.value
    const deltaY = endY.value - startY.value
    const absDeltaX = Math.abs(deltaX)
    const absDeltaY = Math.abs(deltaY)
    
    // Minimum swipe distance
    const minSwipeDistance = 50
    
    if (absDeltaX > minSwipeDistance || absDeltaY > minSwipeDistance) {
      const direction = absDeltaX > absDeltaY 
        ? (deltaX > 0 ? 'right' : 'left')
        : (deltaY > 0 ? 'down' : 'up')
      
      // Dispatch custom event
      if (element.value) {
        element.value.dispatchEvent(new CustomEvent('swipe', {
          detail: { direction, deltaX, deltaY }
        }))
      }
    }
    
    isSwiping.value = false
  }
  
  onMounted(() => {
    if (element.value) {
      element.value.addEventListener('touchstart', touchStart, { passive: true })
      element.value.addEventListener('touchmove', touchMove, { passive: true })
      element.value.addEventListener('touchend', touchEnd, { passive: true })
    }
  })
  
  onUnmounted(() => {
    if (element.value) {
      element.value.removeEventListener('touchstart', touchStart)
      element.value.removeEventListener('touchmove', touchMove)
      element.value.removeEventListener('touchend', touchEnd)
    }
  })
  
  return {
    startX,
    startY,
    endX,
    endY,
    isSwiping
  }
}

export function useLongPress(callback, delay = 500) {
  let timeoutId = null
  
  const start = (e) => {
    e.preventDefault()
    timeoutId = setTimeout(() => {
      callback(e)
    }, delay)
  }
  
  const clear = () => {
    if (timeoutId) {
      clearTimeout(timeoutId)
      timeoutId = null
    }
  }
  
  return {
    onPointerDown: start,
    onPointerUp: clear,
    onPointerLeave: clear,
    onTouchStart: start,
    onTouchEnd: clear,
    onTouchCancel: clear
  }
}
