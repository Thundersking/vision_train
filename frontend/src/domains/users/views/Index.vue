<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useUserStore} from '@/domains/users/stores/user.js'

const router = useRouter()
const store = useUserStore()

const columns = ref([
  {field: 'last_name', header: 'Фамилия'},
  {field: 'first_name', header: 'Имя'},
  {field: 'email', header: 'Email'},
])

const actions = ref([
  {
    label: 'Просмотр',
    icon: 'pi pi-eye',
    severity: 'info',
    callback: (row) => router.push({name: 'user-show', params: {uuid: row.uuid}})
  },
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    severity: 'info',
    callback: (row) => router.push({name: 'user-update', params: {uuid: row.uuid}})
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
  <div>
    <TitleBlock title="Пользователи">
      <template #actions>
        <Button
            label="Добавить пользователя"
            icon="pi pi-plus"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions"/>
    </Card>
  </div>
</template>
