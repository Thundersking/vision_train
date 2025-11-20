<script setup>
</script>

<template>
  <div>
    <TitleBlock title="Просмотр" :back-to="{name: 'users'}">
      <template #actions>
        <Button
            label="Редактировать"
            icon="pi pi-pencil"
        />
      </template>
    </TitleBlock>
  </div>
</template>

<style scoped>

</style>