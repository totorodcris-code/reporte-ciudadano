<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
      <div class="flex items-center gap-2">
        <div class="w-3 h-3 rounded-full" :class="colorClass"></div>
        <span class="text-sm text-gray-600">{{ total }} total</span>
      </div>
    </div>
    
    <div class="relative h-64">
      <canvas ref="chartCanvas"></canvas>
    </div>
    
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
    
    <div v-if="!loading && data.length === 0" class="flex items-center justify-center h-64 text-gray-500">
      <p>No hay datos disponibles</p>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js'

export default {
  name: 'PieChart',
  props: {
    title: {
      type: String,
      required: true
    },
    data: {
      type: Array,
      required: true
    },
    colors: {
      type: Array,
      default: () => [
        '#3B82F6', '#10B981', '#F59E0B', '#06B6D4', '#84CC16', '#EF4444', '#8B5CF6'
      ]
    }
  },
  data() {
    return {
      chart: null,
      loading: false
    }
  },
  mounted() {
    this.initChart()
  },
  watch: {
    data: {
      handler() {
        this.updateChart()
      },
      deep: true
    }
  },
  methods: {
    initChart() {
      const ctx = this.$refs.chartCanvas.getContext('2d')
      
      this.chart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: this.data.map(item => item.label),
          datasets: [{
            data: this.data.map(item => item.value),
            backgroundColor: this.colors,
            borderColor: '#ffffff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                  size: 12
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#fff',
              bodyColor: '#fff',
              borderColor: '#ddd',
              borderWidth: 1,
              displayColors: false,
              callbacks: {
                label: function(context) {
                  const label = context.label || ''
                  const value = context.parsed || 0
                  const total = context.dataset.data.reduce((a, b) => a + b, 0)
                  const percentage = ((value / total) * 100).toFixed(1)
                  return `${label}: ${value} (${percentage}%)`
                }
              }
            }
          }
        }
      })
    },
    
    updateChart() {
      if (!this.chart) return
      
      this.loading = true
      
      setTimeout(() => {
        this.chart.data.datasets[0].data = this.data.map(item => item.value)
        this.chart.data.labels = this.data.map(item => item.label)
        this.chart.update()
        this.loading = false
      }, 500)
    },
    
    destroyChart() {
      if (this.chart) {
        this.chart.destroy()
        this.chart = null
      }
    }
  },
  
  beforeUnmount() {
    this.destroyChart()
  }
}
</script>
