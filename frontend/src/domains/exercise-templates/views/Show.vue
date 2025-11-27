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

const steps = computed(() => template.value?.steps ?? [])
const parameters = computed(() => template.value?.parameters ?? [])
const extraPayload = computed(() => {
  const payload = template.value?.extra_payload
  if (!payload || typeof payload !== 'object') {
    return []
  }

  return Object.entries(payload).map(([key, value]) => ({ key, value }))
})

const formattedDuration = computed(() => {
  const seconds = Number(template.value?.duration_seconds)
  if (!Number.isFinite(seconds) || seconds <= 0) {
    return '—'
  }

  const minutes = Math.floor(seconds / 60)
  const restSeconds = seconds % 60

  if (!minutes) {
    return `${restSeconds} сек`
  }

  if (!restSeconds) {
    return `${minutes} мин`
  }

  return `${minutes} мин ${restSeconds} сек`
})

const formatValue = (value) => {
  if (typeof value === 'boolean') {
    return value ? 'Да' : 'Нет'
  }
  if (typeof value === 'number') {
    return value
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
          <div>
            <p class="text-sm text-gray-500">Общая длительность</p>
            <p class="font-medium">{{ formattedDuration }}</p>
          </div>
        </div>

        <div class="mt-8 space-y-6">
          <section>
            <h3 class="text-lg font-semibold mb-3">Основные параметры</h3>
            <div class="space-y-3">
              <div class="border rounded-lg p-3" v-if="template.instructions">
                <p class="text-sm text-gray-500">Инструкции</p>
                <p class="font-medium whitespace-pre-line">{{ template.instructions }}</p>
              </div>
              <div
                v-for="detail in extraPayload"
                :key="detail.key"
                class="border rounded-lg p-3"
              >
                <p class="text-sm text-gray-500">{{ detail.key }}</p>
                <p class="font-medium">{{ formatValue(detail.value) }}</p>
              </div>
              <p v-if="!template.instructions && !extraPayload.length" class="text-sm text-gray-500">
                Нет дополнительных параметров
              </p>
            </div>
          </section>

          <section>
            <div class="flex items-center justify-between mb-3">
              <h3 class="text-lg font-semibold">Целевые параметры</h3>
              <span class="text-sm text-gray-500" v-if="parameters.length">{{ parameters.length }}</span>
            </div>
            <div v-if="parameters.length" class="space-y-3">
              <div
                v-for="parameter in parameters"
                :key="parameter.key || parameter.label || parameter.unit"
                class="border rounded-lg p-3"
              >
                <p class="font-semibold">{{ parameter.label || parameter.key || 'Параметр' }}</p>
                <p class="text-sm text-gray-500 mt-1">
                  Цель: {{ parameter.target_value || '—' }}
                  <span v-if="parameter.unit">{{ parameter.unit }}</span>
                </p>
              </div>
            </div>
            <p v-else class="text-sm text-gray-500">Целевые параметры не заданы</p>
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
