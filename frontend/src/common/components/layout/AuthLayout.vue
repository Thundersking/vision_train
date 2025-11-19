<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-cyan-100 flex items-center justify-center p-4 dark:from-gray-900 dark:to-gray-800">
    <div class="w-full max-w-md">
      <slot />
    </div>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import { useThemeStore } from '@/common/stores/theme.js'

export default {
  name: 'AuthLayout',
  setup() {
    const themeStore = useThemeStore()

    onMounted(() => {
      // Инициализируем тему для страниц авторизации
      themeStore.initializeTheme()
    })

    return {
      themeStore
    }
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>