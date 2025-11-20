<script setup>
import {ref} from 'vue'
import TitleBlock from '@/common/components/ui/TitleBlock.vue'
import BaseDataTable from '@/common/components/table/BaseDataTable.vue'
import Button from 'primevue/button'
import {useUserStore} from '@/domains/users/stores/user.js'

const store = useUserStore()

const columns = ref([
  {field: 'last_name', header: 'Фамилия'},
  {field: 'first_name', header: 'Имя'},
  {field: 'email', header: 'Email'},
])

const actions = ref([
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    severity: 'info',
    callback: (row) => console.log('Edit:', row)
  },
  {
    label: 'Удалить',
    icon: 'pi pi-trash',
    severity: 'danger',
    callback: (row) => console.log('Delete:', row)
  }
])
</script>

<template>
  <div class="space-y-6">
    <TitleBlock title="Пользователи">
      <template #actions>
        <Button
            label="Добавить пользователя"
            icon="pi pi-plus"
            class="bg-sky-500 hover:bg-sky-600"
        />
      </template>
    </TitleBlock>

    <BaseDataTable :store="store" :columns="columns" :actions="actions"/>
  </div>
</template>
