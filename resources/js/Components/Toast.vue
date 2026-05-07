<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="transform translate-x-full opacity-0"
    enter-to-class="transform translate-x-0 opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="transform translate-x-0 opacity-100"
    leave-to-class="transform translate-x-full opacity-0"
  >
    <div
      v-if="show"
      class="fixed top-4 right-4 z-50 max-w-sm w-full"
      :class="[containerClass]"
    >
      <div
        class="rounded-2xl shadow-xl p-4 border backdrop-blur-md"
        :class="[
          typeClasses[type],
          borderClasses[type]
        ]"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i
              class="text-xl"
              :class="[iconClasses[type]]"
            ></i>
          </div>
          <div class="ml-3 flex-1">
            <h3
              v-if="title"
              class="text-sm font-semibold mb-1"
              :class="titleClasses[type]"
            >
              {{ title }}
            </h3>
            <p
              class="text-sm leading-relaxed"
              :class="messageClasses[type]"
            >
              {{ message }}
            </p>
          </div>
          <button
            @click="close"
            class="ml-4 flex-shrink-0 p-1 rounded-lg transition-colors duration-200"
            :class="closeButtonClasses[type]"
          >
            <i class="fas fa-times text-sm"></i>
          </button>
        </div>
        
        <!-- Progress bar -->
        <div
          v-if="autoClose && duration > 0"
          class="mt-3 h-1 rounded-full overflow-hidden"
          :class="progressBarClasses[type]"
        >
          <div
            class="h-full transition-all ease-linear"
            :class="progressBarFillClasses[type]"
            :style="{
              width: progressWidth + '%',
              transitionDuration: duration + 'ms'
            }"
          ></div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'Toast',
  props: {
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
      type: String,
      default: ''
    },
    message: {
      type: String,
      required: true
    },
    duration: {
      type: Number,
      default: 5000
    },
    autoClose: {
      type: Boolean,
      default: true
    },
    containerClass: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      show: false,
      progressWidth: 100,
      timer: null,
      progressTimer: null
    }
  },
  computed: {
    typeClasses() {
      return {
        success: 'bg-green-50',
        error: 'bg-red-50',
        warning: 'bg-yellow-50',
        info: 'bg-blue-50'
      }
    },
    borderClasses() {
      return {
        success: 'border-green-200',
        error: 'border-red-200',
        warning: 'border-yellow-200',
        info: 'border-blue-200'
      }
    },
    iconClasses() {
      return {
        success: 'fas fa-check-circle text-green-600',
        error: 'fas fa-exclamation-circle text-red-600',
        warning: 'fas fa-exclamation-triangle text-yellow-600',
        info: 'fas fa-info-circle text-blue-600'
      }
    },
    titleClasses() {
      return {
        success: 'text-green-900',
        error: 'text-red-900',
        warning: 'text-yellow-900',
        info: 'text-blue-900'
      }
    },
    messageClasses() {
      return {
        success: 'text-green-800',
        error: 'text-red-800',
        warning: 'text-yellow-800',
        info: 'text-blue-800'
      }
    },
    closeButtonClasses() {
      return {
        success: 'hover:bg-green-100 text-green-600',
        error: 'hover:bg-red-100 text-red-600',
        warning: 'hover:bg-yellow-100 text-yellow-600',
        info: 'hover:bg-blue-100 text-blue-600'
      }
    },
    progressBarClasses() {
      return {
        success: 'bg-green-200',
        error: 'bg-red-200',
        warning: 'bg-yellow-200',
        info: 'bg-blue-200'
      }
    },
    progressBarFillClasses() {
      return {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
      }
    }
  },
  methods: {
    open() {
      this.show = true
      this.progressWidth = 100
      
      if (this.autoClose && this.duration > 0) {
        this.startProgress()
        this.timer = setTimeout(() => {
          this.close()
        }, this.duration)
      }
    },
    close() {
      this.show = false
      this.clearTimers()
    },
    startProgress() {
      const startTime = Date.now()
      const endTime = startTime + this.duration
      
      this.progressTimer = setInterval(() => {
        const now = Date.now()
        const remaining = Math.max(0, endTime - now)
        this.progressWidth = (remaining / this.duration) * 100
        
        if (remaining <= 0) {
          this.clearTimers()
        }
      }, 50)
    },
    clearTimers() {
      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }
      if (this.progressTimer) {
        clearInterval(this.progressTimer)
        this.progressTimer = null
      }
    }
  },
  mounted() {
    this.open()
  },
  beforeUnmount() {
    this.clearTimers()
  }
}
</script>
