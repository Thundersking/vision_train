<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {useDeleteConfirmation} from '@/common/composables/useDeleteConfirmation.js'

const router = useRouter()
const store = useDepartmentsStore()
const {confirmDelete} = useDeleteConfirmation()

const columns = ref([
  {field: 'name', header: 'Название'},
  {field: 'email', header: 'Email'},
  {field: 'phone', header: 'Телефон'},
  {field: 'is_active', header: 'Статус', slot: 'status'},
])

const handleDelete = async (department) => {
  await confirmDelete({
    deleteFn: () => store.delete(department.uuid),
    onSuccess: () => store.index()
  })
}

const actions = ref([
  {
    label: 'Просмотр',
    icon: 'pi pi-eye',
    severity: 'info',
    callback: (row) => router.push({name: 'department-show', params: {uuid: row.uuid}})
  },
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    severity: 'info',
    callback: (row) => router.push({name: 'department-update', params: {uuid: row.uuid}})
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
    <TitleBlock title="Офисы">
      <template #actions>
        <Button
            label="Добавить офис"
            icon="pi pi-plus"
            @click="router.push({name: 'department-create'})"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions">
        <template #status="{ row }">
          <Tag
              :value="row.is_active ? 'Активен' : 'Неактивен'"
              :severity="row.is_active ? 'success' : 'danger'"
          />
        </template>
      </BaseDataTable>
    </Card>
  </div>
</template>
