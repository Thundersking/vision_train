<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <div class="bg-white p-4 dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div v-if="loading" class="flex flex-col items-center justify-center py-8">
      <i class="pi pi-spin pi-spinner text-2xl text-gray-500 mb-2"></i>
      <p class="text-gray-600">Загрузка...</p>
    </div>
    <slot v-else />
  </div>
</template>
