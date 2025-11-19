<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
          Пациенты
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Управление пациентами и их медицинскими данными.
        </p>
      </div>
      <Button 
        label="Добавить пациента" 
        icon="pi pi-plus" 
        class="bg-sky-500 hover:bg-sky-600"
      />
    </div>

    <!-- Patients Table Placeholder -->
    <div class="rounded-lg border border-gray-200 bg-white shadow dark:border-gray-800 dark:bg-gray-900">
      <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
          Список пациентов
        </h3>
      </div>
      <div class="p-6">
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg dark:bg-gray-800">
          <div class="text-center">
            <svg 
              class="mx-auto h-12 w-12 text-gray-400" 
              fill="none" 
              stroke="currentColor" 
              viewBox="0 0 24 24"
            >
              <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
              Нет пациентов
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              Начните с добавления первого пациента в систему.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PatientsIndex',
  data() {
    return {
      patients: []
    }
  },
  mounted() {
    console.log('Patients page mounted')
  }
}
</script>