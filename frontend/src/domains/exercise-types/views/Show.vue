<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'

const route = useRoute()
const router = useRouter()
const store = useExerciseTypeStore()

const type = ref(null)
const loading = ref(true)

const loadData = async () => {
  loading.value = true
  try {
    type.value = await store.show(route.params.uuid)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

const metricsList = computed(() => {
  if (!type.value?.metrics) return []
  const metrics = type.value.metrics
  if (Array.isArray(metrics)) {
    return metrics
  }
  if (Array.isArray(metrics.metrics)) {
    return metrics.metrics
  }
  return Object.entries(metrics).map(([key, value]) => ({
    key,
    label: key,
    description: typeof value === 'object' ? JSON.stringify(value) : value
  }))
})
</script>

<template>
  <div>
    <TitleBlock
      :title="type?.name || 'Тип упражнения'"
      :description="type?.description"
      :back-to="{ name: 'exercise-types' }"
    >
      <template #actions>
        <Button
          label="Редактировать"
          icon="pi pi-pencil"
          @click="router.push({ name: 'exercise-type-update', params: { uuid: route.params.uuid } })"
          :disabled="loading"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="type">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-sm text-gray-500">Формат</p>
            <Tag :value="type.dimension?.toUpperCase()" severity="info" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Слаг</p>
            <p class="font-medium">{{ type.slug }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Статус</p>
            <Tag
              :value="type.is_active ? 'Активен' : 'Выключен'"
              :severity="type.is_active ? 'success' : 'danger'"
            />
          </div>
          <div>
            <p class="text-sm text-gray-500">Настраиваемый</p>
            <Tag
              :value="type.is_customizable ? 'Да' : 'Нет'"
              :severity="type.is_customizable ? 'success' : 'secondary'"
            />
          </div>
        </div>

        <div class="mt-8">
          <h3 class="text-lg font-semibold mb-3">Метрики</h3>
          <div v-if="metricsList.length" class="space-y-3">
            <div
              v-for="(metric, index) in metricsList"
              :key="index"
              class="border rounded-lg p-3"
            >
              <p class="font-semibold">
                {{ metric.label || metric.key || metric.name || `Метрика #${index + 1}` }}
              </p>
              <p v-if="metric.unit" class="text-sm text-gray-500">Ед.: {{ metric.unit }}</p>
              <p v-if="metric.description" class="text-sm text-gray-600 mt-1">{{ metric.description }}</p>
              <div v-else-if="metric.thresholds" class="text-sm text-gray-600 mt-1">
                Диапазон: {{ metric.thresholds?.min }} - {{ metric.thresholds?.max }}
              </div>
            </div>
          </div>
          <p v-else class="text-sm text-gray-500">Метрики не заполнены</p>
        </div>
      </div>
    </Card>
  </div>
</template>
