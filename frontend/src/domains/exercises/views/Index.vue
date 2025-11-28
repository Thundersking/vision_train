<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {useDeleteConfirmation} from '@/common/composables/useDeleteConfirmation.js'

const router = useRouter()
const store = useExerciseStore()
const {confirmDelete} = useDeleteConfirmation()

const columns = ref([
  {field: 'exercise_type_label', header: 'Тип'},
  {field: 'patient.full_name', header: 'Пациент'},
  {field: 'template.title', header: 'Шаблон'},
  {field: 'duration_seconds', header: 'Длительность (сек)'},
  {field: 'started_at', header: 'Начало', type: 'date'},
  {field: 'completed_at', header: 'Завершение', type: 'date'}
])

const handleDelete = async (exercise) => {
  await confirmDelete({
    deleteFn: () => store.delete(exercise.uuid),
    entityName: 'упражнения',
    targetUuid: exercise.uuid,
    onSuccess: () => store.index()
  })
}

const actions = ref([
  {
    label: 'Просмотр',
    icon: 'pi pi-eye',
    severity: 'info',
    callback: (row) => router.push({name: 'exercise-show', params: {uuid: row.uuid}})
  },
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    severity: 'info',
    callback: (row) => router.push({name: 'exercise-update', params: {uuid: row.uuid}})
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
    <TitleBlock title="Упражнения">
      <template #actions>
        <Button
            label="Добавить упражнение"
            icon="pi pi-plus"
            @click="router.push({name: 'exercise-create'})"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions"/>
    </Card>
  </div>
</template>

