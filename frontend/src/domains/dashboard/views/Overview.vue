<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
        Dashboard
      </h1>
      <p class="text-gray-600 dark:text-gray-400">
        Добро пожаловать в систему Vision Train. Обзор основных показателей.
      </p>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
      <!-- Total Patients Card -->
      <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              Всего пациентов
            </p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
              1,247
            </h3>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-sky-50 dark:bg-sky-500/10">
            <i class="pi pi-users h-6 w-6 text-sky-500"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="inline-flex items-center text-xs font-medium text-success-600">
            <i class="pi pi-arrow-up mr-1 h-3 w-3 text-current"></i>
            +12% с прошлого месяца
          </span>
        </div>
      </div>

      <!-- Active Sessions Card -->
      <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              Активные сессии
            </p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
              89
            </h3>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-50 dark:bg-green-500/10">
            <i class="pi pi-bolt h-6 w-6 text-green-500"></i>
          </div>
        </div>
        <div class="mt-4">
          <span class="inline-flex items-center text-xs font-medium text-success-600">
            <i class="pi pi-arrow-up mr-1 h-3 w-3 text-current"></i>
            +5% с прошлой недели
          </span>
        </div>
      </div>

      <!-- Completed Tasks Card -->
      <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              Выполнено задач
            </p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
              342
            </h3>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-500/10">
            <svg
              class="h-6 w-6 text-orange-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <span class="inline-flex items-center text-xs font-medium text-warning-600">
            <svg class="mr-1 h-3 w-3 rotate-180" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
            -2% с прошлого месяца
          </span>
        </div>
      </div>

      <!-- Revenue Card -->
      <div class="rounded-lg border border-gray-200 bg-white p-6 shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
              Доход
            </p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">
              ₽1,234,567
            </h3>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-error-50 dark:bg-error-500/10">
            <svg
              class="h-6 w-6 text-error-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"
              />
            </svg>
          </div>
        </div>
        <div class="mt-4">
          <span class="inline-flex items-center text-xs font-medium text-success-600">
            <i class="pi pi-arrow-up mr-1 h-3 w-3 text-current"></i>
            +8% с прошлого квартала
          </span>
        </div>
      </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
      <!-- Recent Activity -->
      <div class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Недавняя активность
          </h3>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-sky-100 flex items-center justify-center">
                  <svg class="h-4 w-4 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-gray-900 dark:text-white">
                  Новый пациент добавлен в систему
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  2 минуты назад
                </p>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                  <svg class="h-4 w-4 text-success-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-gray-900 dark:text-white">
                  Тренировочная сессия завершена успешно
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  15 минут назад
                </p>
              </div>
            </div>

            <div class="flex items-start space-x-3">
              <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-warning-100 flex items-center justify-center">
                  <svg class="h-4 w-4 text-warning-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"/>
                    <path d="M11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                  </svg>
                </div>
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm text-gray-900 dark:text-white">
                  Требуется обновление данных пациента
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                  1 час назад
                </p>
              </div>
            </div>
          </div>
          
          <div class="mt-6">
            <router-link
              to="/reports"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              Посмотреть все
            </router-link>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Быстрые действия
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <router-link
              to="/patients"
              class="group relative rounded-lg border border-gray-200 p-4 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600"
            >
              <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-sky-50 group-hover:bg-sky-100 dark:bg-sky-500/10 dark:group-hover:bg-sky-500/20">
                <svg class="h-6 w-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
              </div>
              <h4 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                Добавить пациента
              </h4>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Создать новую запись
              </p>
            </router-link>

            <router-link
              to="/reports"
              class="group relative rounded-lg border border-gray-200 p-4 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600"
            >
              <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-50 group-hover:bg-green-100 dark:bg-green-500/10 dark:group-hover:bg-green-500/20">
                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <h4 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                Отчеты
              </h4>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Просмотр статистики
              </p>
            </router-link>

            <router-link
              to="/users"
              class="group relative rounded-lg border border-gray-200 p-4 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600"
            >
              <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-orange-50 group-hover:bg-orange-100 dark:bg-orange-500/10 dark:group-hover:bg-orange-500/20">
                <svg class="h-6 w-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <h4 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                Пользователи
              </h4>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Управление доступом
              </p>
            </router-link>

            <router-link
              to="/dashboard/analytics"
              class="group relative rounded-lg border border-gray-200 p-4 hover:border-sky-300 hover:shadow-md dark:border-gray-700 dark:hover:border-sky-600"
            >
              <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-error-50 group-hover:bg-error-100 dark:bg-error-500/10 dark:group-hover:bg-error-500/20">
                <svg class="h-6 w-6 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
              </div>
              <h4 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">
                Аналитика
              </h4>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Детальная аналитика
              </p>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardOverview',
  data() {
    return {
      // Можно добавить reactive данные при необходимости
    }
  },
  mounted() {
    // Здесь можно загрузить данные для дашборда
    console.log('Dashboard Overview mounted')
  }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>