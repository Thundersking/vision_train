<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'
import { useDeleteConfirmation } from '@/common/composables/useDeleteConfirmation.js'

const router = useRouter()
const store = useExerciseTypeStore()
const { confirmDelete } = useDeleteConfirmation()

const columns = ref([
  { field: 'name', header: 'Название' },
  { field: 'slug', header: 'Слаг' },
  { field: 'dimension', header: 'Формат', slot: 'dimension' },
  { field: 'is_active', header: 'Статус', slot: 'status' }
])

const handleDelete = async (type) => {
  await confirmDelete({
    deleteFn: () => store.destroy(type.uuid),
    entityName: 'тип упражнения',
    entityTitle: type.name,
    onSuccess: () => store.index()
  })
}

const actions = ref([
  {
    label: 'Просмотр',
    icon: 'pi pi-eye',
    callback: (row) => router.push({ name: 'exercise-type-show', params: { uuid: row.uuid } })
  },
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    callback: (row) => router.push({ name: 'exercise-type-update', params: { uuid: row.uuid } })
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
    <TitleBlock title="Типы упражнений">
      <template #actions>
        <Button
          label="Добавить тип"
          icon="pi pi-plus"
          @click="router.push({ name: 'exercise-type-create' })"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions">
        <template #dimension="{ row }">
          <Tag
            :value="row.dimension?.toUpperCase()"
            severity="info"
          />
        </template>
        <template #status="{ row }">
          <Tag
            :value="row.is_active ? 'Активен' : 'Выключен'"
            :severity="row.is_active ? 'success' : 'danger'"
          />
        </template>
      </BaseDataTable>
    </Card>
  </div>
</template>
