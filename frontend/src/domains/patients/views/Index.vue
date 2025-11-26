<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { usePatientStore } from '@/domains/patients/stores/patient.js'
import { useDeleteConfirmation } from '@/common/composables/useDeleteConfirmation.js'
import { formatDate } from '@/common/utils/date.js'

const router = useRouter()
const store = usePatientStore()
const { confirmDelete } = useDeleteConfirmation()

const columns = ref([
  { field: 'full_name', header: 'Пациент' },
  { field: 'phone', header: 'Телефон' },
  { field: 'doctor', header: 'Врач', slot: 'doctor' },
  { field: 'is_active', header: 'Статус', slot: 'status' },
  { field: 'created_at', header: 'Создан', slot: 'created' }
])

const handleDelete = async (patient) => {
  await confirmDelete({
    deleteFn: () => store.destroy(patient.uuid),
    entityName: 'пациента',
    entityTitle: patient.full_name,
    onSuccess: () => store.index()
  })
}

const actions = ref([
  {
    label: 'Просмотреть',
    icon: 'pi pi-eye',
    severity: 'secondary',
    callback: (row) => router.push({ name: 'patient-show', params: { uuid: row.uuid } })
  },
  {
    label: 'Редактировать',
    icon: 'pi pi-pencil',
    severity: 'secondary',
    callback: (row) => router.push({ name: 'patient-update', params: { uuid: row.uuid } })
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
  <div class="space-y-6">
    <TitleBlock title="Пациенты">
      <template #actions>
        <Button
          label="Добавить пациента"
          icon="pi pi-plus"
          @click="router.push({ name: 'patient-create' })"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions">
        <template #doctor="{ row }">
          {{ row.doctor?.full_name || '—' }}
        </template>

        <template #status="{ row }">
          <Tag :severity="row.is_active ? 'success' : 'danger'">
            {{ row.is_active ? 'Активен' : 'Неактивен' }}
          </Tag>
        </template>

        <template #created="{ row }">
          {{ formatDate(row.created_at) }}
        </template>
      </BaseDataTable>
    </Card>
  </div>
</template>
