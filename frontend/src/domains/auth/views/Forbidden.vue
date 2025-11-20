<script setup>

</script>

<template>
  <div class="flex items-center justify-center p-6">
    <Card>
      <div class="p-12 text-center space-y-6">
        <div class="text-6xl">🔒</div>
        <h1 class="text-3xl font-semibold text-slate-900 dark:text-slate-50">
          Доступ запрещен
        </h1>
        <p class="text-slate-600 dark:text-slate-400">
          У вас нет прав доступа к этому ресурсу
        </p>
        <Button
            label="На главную"
            icon="pi pi-home"
            @click="$router.push('/')"
        />
      </div>
    </Card>
  </div>
</template>

<style scoped>

</style>