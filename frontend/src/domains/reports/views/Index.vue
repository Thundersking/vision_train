<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
        Отчеты
      </h1>
      <p class="text-gray-600 dark:text-gray-400">
        Создание и просмотр медицинских и статистических отчетов.
      </p>
    </div>

    <!-- Reports Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Medical Reports -->
      <router-link
        to="/reports/medical"
        class="group block rounded-lg border border-gray-200 p-6 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600 bg-white dark:bg-gray-900"
      >
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="h-12 w-12 rounded-lg bg-sky-50 flex items-center justify-center group-hover:bg-sky-100 dark:bg-sky-500/10 dark:group-hover:bg-sky-500/20">
              <i class="pi pi-file-text h-6 w-6 text-sky-500"></i>
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              Медицинские отчеты
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Отчеты по диагностике и лечению пациентов
            </p>
          </div>
        </div>
        <div class="mt-4 text-sm text-sky-600 group-hover:text-sky-500">
          Перейти к медицинским отчетам →
        </div>
      </router-link>

      <!-- Statistical Reports -->
      <router-link
        to="/reports/statistics"
        class="group block rounded-lg border border-gray-200 p-6 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600 bg-white dark:bg-gray-900"
      >
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="h-12 w-12 rounded-lg bg-green-50 flex items-center justify-center group-hover:bg-green-100 dark:bg-green-500/10 dark:group-hover:bg-green-500/20">
              <i class="pi pi-chart-line h-6 w-6 text-green-500"></i>
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              Статистические отчеты
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Аналитика и статистика использования системы
            </p>
          </div>
        </div>
        <div class="mt-4 text-sm text-sky-600 group-hover:text-sky-500">
          Перейти к статистическим отчетам →
        </div>
      </router-link>
    </div>

    <!-- Recent Reports -->
    <div class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Последние отчеты
        </h3>
      </div>
      <div class="p-6">
        <div class="h-32 flex items-center justify-center bg-gray-50 rounded-lg dark:bg-gray-800">
          <p class="text-gray-500 dark:text-gray-400">
            Здесь будет список последних отчетов
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ReportsIndex',
  data() {
    return {
      recentReports: []
    }
  },
  mounted() {
    console.log('Reports page mounted')
  }
}
</script>