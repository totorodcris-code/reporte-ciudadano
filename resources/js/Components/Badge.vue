<template>
  <span
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-all duration-200"
    :class="[
      sizeClasses[size],
      variantClasses[variant],
      { 'animate-pulse': pulse }
    ]"
  >
    <i
      v-if="icon"
      :class="[icon, 'mr-1']"
    ></i>
    <slot>{{ text }}</slot>
    <span
      v-if="count !== null"
      class="ml-1 px-1.5 py-0.5 rounded-full text-xs font-bold"
      :class="countClasses[variant]"
    >
      {{ count }}
    </span>
  </span>
</template>

<script>
export default {
  name: 'Badge',
  props: {
    variant: {
      type: String,
      default: 'primary',
      validator: (value) => ['primary', 'secondary', 'success', 'warning', 'error', 'info'].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    text: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: ''
    },
    count: {
      type: Number,
      default: null
    },
    pulse: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    variantClasses() {
      return {
        primary: 'bg-blue-100 text-blue-800 border border-blue-200',
        secondary: 'bg-gray-100 text-gray-800 border border-gray-200',
        success: 'bg-green-100 text-green-800 border border-green-200',
        warning: 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        error: 'bg-red-100 text-red-800 border border-red-200',
        info: 'bg-cyan-100 text-cyan-800 border border-cyan-200'
      }
    },
    sizeClasses() {
      return {
        sm: 'text-xs px-2 py-0.5',
        md: 'text-sm px-2.5 py-0.5',
        lg: 'text-base px-3 py-1'
      }
    },
    countClasses() {
      return {
        primary: 'bg-blue-200 text-blue-900',
        secondary: 'bg-gray-200 text-gray-900',
        success: 'bg-green-200 text-green-900',
        warning: 'bg-yellow-200 text-yellow-900',
        error: 'bg-red-200 text-red-900',
        info: 'bg-cyan-200 text-cyan-900'
      }
    }
  }
}
</script>
