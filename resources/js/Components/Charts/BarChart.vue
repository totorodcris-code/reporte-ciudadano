<template>
  <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
      <div class="flex items-center gap-2">
        <button 
          v-for="period in periods" 
          :key="period.value"
          @click="selectedPeriod = period.value"
          :class="[
            'px-3 py-1 text-sm rounded-lg transition-all',
            selectedPeriod === period.value 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
          ]"
        >
          {{ period.label }}
        </button>
      </div>
    </div>
    
    <div class="relative h-64">
      <canvas ref="chartCanvas"></canvas>
    </div>
    
    <div v-if="loading" class="flex items-center justify-center h-64">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>
  </div>
</template>

<script>
import { Chart } from 'chart.js'

export default {
  name: 'BarChart',
  props: {
    title: {
      type: String,
      required: true
    },
    data: {
      type: Array,
      required: true
    },
    labels: {
      type: Array,
      required: true
    },
    color: {
      type: String,
      default: 'blue'
    },
    periods: {
      type: Array,
      default: () => [
        { label: '7 dias', value: '7d' },
        { label: '30 dias', value: '30d' },
        { label: '3 meses', value: '3m' },
        { label: '6 meses', value: '6m' }
      ]
    }
  },
  data() {
    return {
      chart: null,
      loading: false,
      selectedPeriod: '30d'
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
    },
    selectedPeriod: {
      handler() {
        this.updateChart()
      }
    }
  },
  methods: {
    initChart() {
      const ctx = this.$refs.chartCanvas.getContext('2d')
      
      const colors = {
        blue: 'rgb(59, 130, 246)',
        green: 'rgb(34, 197, 94)',
        yellow: 'rgb(250, 204, 21)',
        red: 'rgb(239, 68, 68)',
        purple: 'rgb(147, 51, 234)'
      }
      
      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: this.labels,
          datasets: [{
            label: this.title,
            data: this.data,
            backgroundColor: colors[this.color] || colors.blue,
            borderColor: colors[this.color] || colors.blue,
            borderWidth: 1,
            borderRadius: 6,
            barThickness: 'flex'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
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
                  return context.dataset.label + ': ' + context.parsed.y
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                color: '#6b7280',
                font: {
                  size: 11
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                color: '#6b7280',
                font: {
                  size: 11
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
        this.chart.data.datasets[0].data = this.data
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
