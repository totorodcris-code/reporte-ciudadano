<template>
  <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
    <div class="flex items-center justify-between mb-3 sm:mb-4">
      <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate pr-2">{{ title }}</h3>
      <div class="flex items-center gap-2 flex-shrink-0">
        <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full shadow-sm" :class="colorClass"></div>
        <span class="text-xs sm:text-sm text-gray-600 font-medium hidden sm:inline">{{ subtitle }}</span>
      </div>
    </div>
    
    <div class="space-y-3 sm:space-y-4">
      <div class="text-2xl sm:text-3xl lg:text-4xl font-bold" :class="textColorClass">
        {{ formatNumber(value) }}
      </div>
      
      <div v-if="change !== null" class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
        <span class="text-xs sm:text-sm text-gray-500">vs período anterior</span>
        <span 
          class="text-xs sm:text-sm font-semibold px-2 py-1 rounded-lg inline-flex items-center"
          :class="change > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
        >
          {{ change > 0 ? '+' : '' }}{{ formatNumber(Math.abs(change)) }}
          {{ changePercent }}
        </span>
      </div>
      
      <div v-if="trend" class="mt-2 sm:mt-3">
        <div class="flex items-center gap-2 p-2 sm:p-3 bg-gray-50 rounded-lg">
          <i class="text-sm sm:text-lg" :class="[trendIcon, trendColorClass]"></i>
          <span class="text-xs sm:text-sm text-gray-700 font-medium">{{ trendText }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnalyticsCard',
  props: {
    title: {
      type: String,
      required: true
    },
    subtitle: {
      type: String,
      required: true
    },
    value: {
      type: Number,
      required: true
    },
    change: {
      type: Number,
      default: null
    },
    changePercent: {
      type: String,
      default: null
    },
    color: {
      type: String,
      default: 'blue'
    },
    trend: {
      type: String,
      default: null
    },
    trendText: {
      type: String,
      default: null
    }
  },
  computed: {
    colorClass() {
      const colors = {
        blue: 'bg-blue-500 shadow-blue-200',
        green: 'bg-green-500 shadow-green-200',
        yellow: 'bg-yellow-500 shadow-yellow-200',
        red: 'bg-red-500 shadow-red-200',
        purple: 'bg-purple-500 shadow-purple-200'
      }
      return colors[this.color] || colors.blue
    },
    textColorClass() {
      const colors = {
        blue: 'text-blue-600',
        green: 'text-green-600',
        yellow: 'text-yellow-600',
        red: 'text-red-600',
        purple: 'text-purple-600'
      }
      return colors[this.color] || colors.blue
    },
    trendIcon() {
      const icons = {
        up: 'fas fa-arrow-up',
        down: 'fas fa-arrow-down',
        stable: 'fas fa-minus'
      }
      return icons[this.trend] || icons.stable
    },
    trendColorClass() {
      const colors = {
        up: 'text-green-600',
        down: 'text-red-600',
        stable: 'text-gray-600'
      }
      return colors[this.trend] || colors.stable
    }
  },
  methods: {
    formatNumber(num) {
      if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M'
      } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K'
      }
      return num.toString()
    }
  }
}
</script>
