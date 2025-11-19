<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="text-center">
      <div class="mx-auto h-32 w-32 text-gray-400">
        <i class="pi pi-exclamation-triangle text-current text-8xl"></i>
      </div>
      
      <h1 class="mt-6 text-title-lg font-bold text-gray-900 dark:text-white">
        404
      </h1>
      
      <p class="mt-2 text-gray-600 dark:text-gray-400">
        Страница не найдена
      </p>
      
      <p class="mt-2 text-gray-500 dark:text-gray-400 text-sm">
        Запрашиваемая страница не существует или была перемещена.
      </p>
      
      <div class="mt-6">
        <router-link
          to="/dashboard"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-sky-500 hover:bg-sky-600"
        >
          Вернуться на главную
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script>