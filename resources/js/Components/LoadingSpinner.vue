<template>
  <div class="flex items-center justify-center" :class="[containerClass, sizeClass]">
    <div class="relative">
      <div 
        class="rounded-full border-2 border-gray-200 animate-spin"
        :class="[
          spinnerClass,
          size === 'sm' ? 'w-4 h-4 border-t-2' : 
          size === 'md' ? 'w-6 h-6 border-t-2' : 
          size === 'lg' ? 'w-8 h-8 border-t-3' : 
          'w-12 h-12 border-t-4'
        ]"
        :style="{
          borderTopColor: color,
          animationDuration: duration
        }"
      ></div>
      <div 
        v-if="showDot"
        class="absolute top-0 left-0 rounded-full"
        :class="[
          dotClass,
          size === 'sm' ? 'w-1 h-1' : 
          size === 'md' ? 'w-1.5 h-1.5' : 
          size === 'lg' ? 'w-2 h-2' : 
          'w-3 h-3'
        ]"
        :style="{ backgroundColor: color }"
      ></div>
    </div>
    <span v-if="text" class="ml-2 text-sm font-medium" :class="textClass">{{ text }}</span>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    color: {
      type: String,
      default: '#3B82F6'
    },
    text: {
      type: String,
      default: ''
    },
    duration: {
      type: String,
      default: '0.8s'
    },
    showDot: {
      type: Boolean,
      default: true
    },
    containerClass: {
      type: String,
      default: ''
    }
  },
  computed: {
    sizeClass() {
      const sizes = {
        sm: 'text-xs',
        md: 'text-sm',
        lg: 'text-base',
        xl: 'text-lg'
      }
      return sizes[this.size] || sizes.md
    },
    spinnerClass() {
      return this.showDot ? '' : 'animate-spin'
    },
    dotClass() {
      return this.showDot ? 'animate-pulse' : ''
    },
    textClass() {
      return `text-gray-600`
    }
  }
}
</script>
