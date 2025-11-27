<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useExerciseTemplateStore } from '@/domains/exercise-templates/stores/exerciseTemplate.js'

const route = useRoute()
const router = useRouter()
const store = useExerciseTemplateStore()

const template = ref(null)
const loading = ref(true)

const loadData = async () => {
  loading.value = true
  try {
    template.value = await store.show(route.params.uuid)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

const parsedPayload = computed(() => {
  if (!template.value?.payload) return null
  if (typeof template.value.payload === 'string') {
    try {
      return JSON.parse(template.value.payload)
    } catch (e) {
      return null
    }
  }
  return template.value.payload
})

const steps = computed(() => {
  if (!parsedPayload.value?.steps || !Array.isArray(parsedPayload.value.steps)) {
    return []
  }
  return parsedPayload.value.steps
})

const payloadDetails = computed(() => {
  if (!parsedPayload.value) return []
  const base = { ...parsedPayload.value }
  delete base.steps
  return Object.entries(base).map(([key, value]) => ({
    key,
    value
  }))
})

const formatValue = (value) => {
  if (typeof value === 'number' || typeof value === 'boolean') {
    return value ? 'Да' : 'Нет'
  }
  if (typeof value === 'string') {
    return value
  }
  if (Array.isArray(value)) {
    return value.map((item) => (typeof item === 'object' ? JSON.stringify(item) : item)).join(', ')
  }
  if (typeof value === 'object' && value !== null) {
    return Object.entries(value)
      .map(([k, v]) => `${k}: ${v}`)
      .join('; ')
  }
  return '—'
}
</script>

<template>
  <div>
    <TitleBlock
      :title="template?.title || 'Шаблон упражнения'"
      :description="template?.short_description"
      :back-to="{ name: 'exercise-templates' }"
    >
      <template #actions>
        <Button
          label="Редактировать"
          icon="pi pi-pencil"
          :disabled="loading"
          @click="router.push({ name: 'exercise-template-update', params: { uuid: route.params.uuid } })"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="template">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-sm text-gray-500">Тип упражнения</p>
            <p class="font-medium">{{ template.type?.name || '—' }}</p>
            <small v-if="template.type?.dimension" class="text-xs text-gray-500">
              Формат: {{ template.type.dimension.toUpperCase() }}
            </small>
          </div>
          <div>
            <p class="text-sm text-gray-500">Сложность</p>
            <Tag :value="template.difficulty || 'Не указана'" />
          </div>
          <div>
            <p class="text-sm text-gray-500">Статус</p>
            <Tag
              :value="template.is_active ? 'Активен' : 'Выключен'"
              :severity="template.is_active ? 'success' : 'danger'"
            />
          </div>
        </div>

        <div class="mt-8 space-y-6">
          <section>
            <h3 class="text-lg font-semibold mb-3">Основные параметры</h3>
            <div v-if="payloadDetails.length" class="space-y-3">
              <div
                v-for="detail in payloadDetails"
                :key="detail.key"
                class="border rounded-lg p-3"
              >
                <p class="text-sm text-gray-500">{{ detail.key }}</p>
                <p class="font-medium">{{ formatValue(detail.value) }}</p>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500">Параметры не заполнены</p>
          </section>

          <section>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold">Шаги сценария</h3>
              <span class="text-sm text-gray-500" v-if="steps.length">{{ steps.length }} шаг(ов)</span>
            </div>
            <div v-if="steps.length" class="space-y-3">
              <div
                v-for="(step, index) in steps"
                :key="index"
                class="border rounded-lg p-3"
              >
                <p class="font-semibold">{{ step.title || `Шаг ${index + 1}` }}</p>
                <p v-if="step.duration" class="text-sm text-gray-500">Длительность: {{ step.duration }} сек.</p>
                <p v-if="step.description" class="text-sm text-gray-600 mt-1">{{ step.description }}</p>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500">Шаги не заданы</p>
          </section>
        </div>
      </div>
    </Card>
  </div>
</template>
