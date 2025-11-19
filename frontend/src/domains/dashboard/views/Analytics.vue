<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
        Аналитика
      </h1>
      <p class="text-gray-600 dark:text-gray-400">
        Подробная аналитика и статистика использования системы Vision Train.
      </p>
    </div>

    <!-- Analytics Content -->
    <div class="grid grid-cols-1 gap-6">
      <!-- Chart Placeholder -->
      <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
          График активности по времени
        </h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg dark:bg-gray-800">
          <p class="text-gray-500 dark:text-gray-400">
            Здесь будет график аналитики (Chart.js / ApexCharts)
          </p>
        </div>
      </div>

      <!-- Statistics Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
          <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Среднее время сессии
          </h4>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
            24:36
          </p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
          <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Процент успешности
          </h4>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
            87.3%
          </p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
          <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">
            Активных пользователей
          </h4>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
            156
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardAnalytics',
  data() {
    return {
      // Analytics data
    }
  },
  mounted() {
    console.log('Dashboard Analytics mounted')
  }
}
</script>