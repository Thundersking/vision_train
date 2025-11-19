<template>
  <div class="flex h-screen overflow-hidden" :class="themeStore.themeClasses">
    <!-- ===== Sidebar Start ===== -->
    <Sidebar />
    <!-- ===== Sidebar End ===== -->

    <!-- ===== Content Area Start ===== -->
    <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      <!-- Small Device Overlay -->
      <div
        v-if="themeStore.sidebarToggle"
        @click="themeStore.closeSidebar"
        class="fixed inset-0 z-999 bg-black/20 lg:hidden"
      />
      <!-- Small Device Overlay End -->

      <!-- ===== Header Start ===== -->
      <Header />
      <!-- ===== Header End ===== -->

      <!-- ===== Main Content Start ===== -->
      <main class="flex-1">
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
          <slot />
        </div>
      </main>
      <!-- ===== Main Content End ===== -->
    </div>
    <!-- ===== Content Area End ===== -->
  </div>
</template>

<script>
import { onMounted, onUnmounted } from 'vue'
import { useThemeStore } from '@/common/stores/theme.js'
import { useSidebar } from '@/common/composables/useSidebar.js'
import Sidebar from './Sidebar.vue'
import Header from './Header.vue'

export default {
  name: 'DashboardLayout',
  components: {
    Sidebar,
    Header
  },
  setup() {
    const themeStore = useThemeStore()
    const { initializeSidebar } = useSidebar()

    let cleanupSidebar = null

    onMounted(() => {
      // Инициализируем тему
      themeStore.initializeTheme()
      
      // Инициализируем обработчики sidebar
      themeStore.initializeHandlers()
      cleanupSidebar = initializeSidebar()
    })

    onUnmounted(() => {
      // Очищаем обработчики
      if (cleanupSidebar) {
        cleanupSidebar()
      }
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