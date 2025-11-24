<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useUserStore} from '@/domains/users/stores/user.js'
import {useDeleteConfirmation} from '@/common/composables/useDeleteConfirmation.js'
import {useAuthStore} from '@/domains/auth/stores/auth.js'

const router = useRouter()
const store = useUserStore()
const authStore = useAuthStore()
const {confirmDelete} = useDeleteConfirmation()

const columns = ref([
  {field: 'last_name', header: 'Фамилия'},
  {field: 'first_name', header: 'Имя'},
  {field: 'email', header: 'Email'},
])

const handleDelete = async (user) => {
  await confirmDelete({
    deleteFn: () => store.delete(user.uuid),
    entityName: 'пользователя',
    currentUserUuid: authStore.user?.uuid,
    targetUuid: user.uuid,
    onSuccess: () => store.index()
  })
}

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
    callback: (row) => handleDelete(row)
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
            @click="router.push({name: 'user-create'})"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions"/>
    </Card>
  </div>
</template>
