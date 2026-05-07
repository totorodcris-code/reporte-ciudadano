<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80"
      @click.self="close"
    >
      <div class="relative w-full max-w-2xl p-6 bg-white rounded-lg">
        <button
          class="absolute text-2xl text-gray-700 top-2 right-2 hover:text-red-500"
          @click="close"
        >
          &times;
        </button>
        <img :src="item.img" class="object-cover w-full mb-4 rounded h-96" loading="lazy" />
        <h3 class="mb-2 text-xl font-bold">{{ item.title }}</h3>
        <p class="mb-2 text-gray-600">{{ item.desc }}</p>
        <p class="text-gray-500"><strong>Client:</strong> {{ item.client }}</p>
        <p class="text-gray-500"><strong>Category:</strong> {{ item.category }}</p>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    visible: Boolean,
    item: Object,
  },
  methods: {
    close() {
      this.$emit('update:visible', false)
    },
  },
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>