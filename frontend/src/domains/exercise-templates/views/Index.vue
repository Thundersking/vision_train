<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useExerciseTemplateStore } from '@/domains/exercise-templates/stores/exerciseTemplate.js'

const router = useRouter()
const store = useExerciseTemplateStore()

const columns = ref([
  { field: 'title', header: 'Название' },
  { field: 'type', header: 'Тип', slot: 'type' },
  { field: 'difficulty', header: 'Сложность', slot: 'difficulty' },
  { field: 'is_active', header: 'Статус', slot: 'status' }
])

const actions = ref([
  {
    label: 'Просмотр',
    icon: 'pi pi-eye',
    callback: (row) => router.push({ name: 'exercise-template-show', params: { uuid: row.uuid } })
  },
])
</script>

<template>
  <div>
    <TitleBlock title="Шаблоны упражнений">
      <template #actions>
        <Button
          label="Добавить шаблон"
          icon="pi pi-plus"
          @click="router.push({ name: 'exercise-template-create' })"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseDataTable :store="store" :columns="columns" :actions="actions">
        <template #type="{ row }">
          <div class="flex flex-col">
            <span class="font-medium">{{ row.type?.name || '—' }}</span>
            <small class="text-xs text-gray-500" v-if="row.type?.dimension">{{ row.type.dimension?.toUpperCase() }}</small>
          </div>
        </template>
        <template #difficulty="{ row }">
          <Tag
            :value="row.difficulty || 'Не указана'"
            severity="secondary"
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
