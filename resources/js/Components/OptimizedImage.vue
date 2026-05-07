<template>
  <div 
    ref="container"
    :class="['optimized-image', { 'is-loading': !hasLoaded, 'has-error': hasError }]"
    :style="containerStyle"
  >
    <img
      v-if="shouldShowImage"
      ref="imageElement"
      :src="optimizedSrc"
      :srcset="srcSet"
      :sizes="responsiveSizes"
      :alt="alt"
      :width="displayWidth"
      :height="displayHeight"
      :class="imageClass"
      :style="imageStyle"
      @load="onImageLoad"
      @error="onImageError"
      loading="lazy"
      decoding="async"
    />
    
    <!-- Loading placeholder -->
    <div v-if="!hasLoaded && !hasError" :class="placeholderClass">
      <slot name="placeholder">
        <div class="flex items-center justify-center w-full h-full bg-gray-100 animate-pulse">
          <i class="fas fa-image text-gray-400 text-2xl"></i>
        </div>
      </slot>
    </div>
    
    <!-- Error fallback -->
    <div v-if="hasError" :class="errorClass">
      <slot name="error">
        <div class="flex items-center justify-center w-full h-full bg-red-50 text-red-600">
          <i class="fas fa-exclamation-triangle text-2xl"></i>
        </div>
      </slot>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useImageOptimization } from '@/Composables/useImageOptimization'

export default {
  name: 'OptimizedImage',
  props: {
    src: {
      type: String,
      required: true
    },
    alt: {
      type: String,
      default: ''
    },
    width: {
      type: [Number, String],
      default: null
    },
    height: {
      type: [Number, String],
      default: null
    },
    quality: {
      type: Number,
      default: 80
    },
    format: {
      type: String,
      default: 'webp'
    },
    sizes: {
      type: Array,
      default: () => [
        { width: 320 },
        { width: 640 },
        { width: 768 },
        { width: 1024 },
        { width: 1280 },
        { width: 1536 }
      ]
    },
    lazy: {
      type: Boolean,
      default: true
    },
    objectFit: {
      type: String,
      default: 'cover'
    },
    aspectRatio: {
      type: [Number, String],
      default: null
    },
    containerClass: {
      type: String,
      default: ''
    },
    imageClass: {
      type: String,
      default: ''
    },
    placeholderClass: {
      type: String,
      default: ''
    },
    errorClass: {
      type: String,
      default: ''
    }
  },
  setup(props) {
    const container = ref(null)
    const imageElement = ref(null)
    const hasLoaded = ref(false)
    const hasError = ref(false)
    
    const {
      optimizeImageUrl,
      generateSrcSet,
      generateResponsiveSizes,
      applyLazyLoading
    } = useImageOptimization()
    
    const optimizedSrc = computed(() => {
      return optimizeImageUrl(props.src, {
        width: props.width,
        height: props.height,
        quality: props.quality,
        format: props.format
      })
    })
    
    const srcSet = computed(() => {
      if (props.sizes.length === 0) return ''
      return generateSrcSet(props.src, props.sizes)
    })
    
    const responsiveSizes = computed(() => {
      return generateResponsiveSizes(props.sizes)
    })
    
    const displayWidth = computed(() => {
      return props.width || null
    })
    
    const displayHeight = computed(() => {
      return props.height || null
    })
    
    const containerStyle = computed(() => {
      const styles = {}
      
      if (props.aspectRatio) {
        styles.aspectRatio = props.aspectRatio
      }
      
      if (props.width && props.height) {
        styles.width = typeof props.width === 'number' ? `${props.width}px` : props.width
        styles.height = typeof props.height === 'number' ? `${props.height}px` : props.height
      }
      
      return styles
    })
    
    const imageStyle = computed(() => {
      return {
        objectFit: props.objectFit,
        width: '100%',
        height: '100%'
      }
    })
    
    const shouldShowImage = computed(() => {
      return !props.lazy || hasLoaded.value
    })
    
    const onImageLoad = () => {
      hasLoaded.value = true
      hasError.value = false
    }
    
    const onImageError = () => {
      hasError.value = true
      hasLoaded.value = false
    }
    
    onMounted(() => {
      if (props.lazy && container.value) {
        applyLazyLoading(container.value, props.src, {
          width: props.width,
          height: props.height,
          quality: props.quality,
          format: props.format
        })
      } else {
        hasLoaded.value = true
      }
    })
    
    // Watch for src changes
    watch(() => props.src, () => {
      hasLoaded.value = false
      hasError.value = false
      
      if (props.lazy && container.value) {
        applyLazyLoading(container.value, props.src, {
          width: props.width,
          height: props.height,
          quality: props.quality,
          format: props.format
        })
      }
    })
    
    return {
      container,
      imageElement,
      hasLoaded,
      hasError,
      optimizedSrc,
      srcSet,
      responsiveSizes,
      displayWidth,
      displayHeight,
      containerStyle,
      imageStyle,
      shouldShowImage,
      onImageLoad,
      onImageError
    }
  }
}
</script>

<style scoped>
.optimized-image {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.optimized-image img {
  transition: opacity 0.3s ease;
}

.optimized-image.is-loading img {
  opacity: 0;
}

.optimized-image.has-error img {
  display: none;
}

.optimized-image .placeholder,
.optimized-image .error {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
