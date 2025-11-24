<script>
import { computed } from 'vue'
import Toast from 'primevue/toast'
import ConfirmDialog from 'primevue/confirmdialog'
import DashboardLayout from '@/common/components/layout/DashboardLayout.vue'
import AuthLayout from '@/common/components/layout/AuthLayout.vue'

export default {
  name: 'App',
  components: {
    Toast,
    ConfirmDialog,
    DashboardLayout,
    AuthLayout
  },
  setup() {
    return {}
  },
  computed: {
    layout() {
      const layoutName = this.$route.meta.layout || 'dashboard'
      return layoutName
    }
  }
}
</script>

<template>
  <div id="app">
    <!-- Dashboard Layout -->
    <DashboardLayout v-if="layout === 'dashboard'">
      <router-view />
    </DashboardLayout>

    <!-- Auth Layout -->
    <AuthLayout v-else-if="layout === 'auth'">
      <router-view />
    </AuthLayout>

    <!-- Default Layout (fallback) -->
    <div v-else class="min-h-screen bg-gray-50 dark:bg-gray-900">
      <router-view />
    </div>

    <!-- Toast & confirm dialog for глобальных уведомлений -->
    <Toast />
    <ConfirmDialog />
  </div>
</template>

<style>
/* Global styles можно добавить здесь при необходимости */
</style>
