<script setup>
import {computed, onMounted, ref, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {EXERCISE_TEMPLATE_TABS} from '@/domains/exercise-templates/constants/constants.js'
import {useTabState} from '@/common/composables/useTabState.js'

const route = useRoute()
const router = useRouter()
const store = useExerciseTemplateStore()

const template = ref(null)
const loading = ref(false)
const tabs = EXERCISE_TEMPLATE_TABS
const activeTab = ref('overview')
useTabState(activeTab)

const templateUuid = computed(() => route.params.uuid)

const loadData = async () => {
  if (!templateUuid.value) {
    return
  }

  loading.value = true
  try {
    template.value = await store.show(templateUuid.value)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

watch(() => templateUuid.value, (uuid, prev) => {
  if (!uuid || uuid === prev) {
    return
  }
  loadData()
})

const typeLabel = computed(() => {
  const type = template.value?.type
  if (!type) {
    return '—'
  }

  const suffix = type.dimension ? ` (${type.dimension.toUpperCase()})` : ''
  return `${type.name}${suffix}`
})

const is3DType = computed(() => {
  return template.value?.type?.dimension === '3d'
})

const extraPayload = computed(() => {
  const payload = template.value?.extra_payload
  if (!payload || typeof payload !== 'object') {
    return []
  }

  return Object.entries(payload).map(([key, value]) => ({key, value}))
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

const formatSpeed = (speed) => {
  const speedMap = {
    slow: 'Медленно',
    medium: 'Средне',
    fast: 'Быстро'
  }
  return speed ? speedMap[speed] || speed : '—'
}

const formatArea = (area) => {
  const areaMap = {
    full: 'Полная',
    top: 'Верхняя',
    bottom: 'Нижняя',
    left: 'Левая',
    right: 'Правая'
  }
  return area ? areaMap[area] || area : '—'
}

const formatDistanceArea = (area) => {
  const areaMap = {
    full: 'Полная',
    near: 'Близко',
    medium: 'Средне',
    far: 'Далеко'
  }
  return area ? areaMap[area] || area : '—'
}

const handleEdit = () => {
  router.push({ name: 'exercise-template-update', params: { uuid: templateUuid.value } })
}
</script>

<template>
  <div class="space-y-6">
    <TitleBlock
        :title="template?.title || 'Шаблон упражнения'"
        :back-to="{ name: 'exercise-templates' }"
    />

    <BaseTabs
        v-model="activeTab"
        :tabs="tabs"
        :loading="{ overview: loading }"
    >
      <template #overview>
        <div v-if="template" class="space-y-8 relative">
          <div class="absolute right-0 -top-2 flex">
            <Button
                icon="pi pi-pencil"
                text
                rounded
                size="small"
                aria-label="Редактировать"
                :disabled="loading || !template"
                @click="handleEdit"
            />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FieldDisplay label="Тип упражнения" :value="typeLabel"/>
            <FieldDisplay
                label="Сложность"
                type="tag"
                :value="template.difficulty || 'Не указана'"
            />
            <FieldDisplay
                label="Статус"
                type="tag"
                :value="template.is_active ? 'Активен' : 'Выключен'"
                :tag-severity="template.is_active ? 'success' : 'danger'"
            />
            <FieldDisplay label="Общая длительность" :value="formattedDuration"/>
          </div>

          <section>
            <h3 class="text-lg font-semibold mb-3">Основные параметры</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FieldDisplay label="Инструкции" :value="template.instructions || '—'"/>
              <FieldDisplay
                  v-for="detail in extraPayload"
                  :key="detail.key"
                  :label="detail.key"
                  :value="formatValue(detail.value)"
              />
            </div>
            <p v-if="!template.instructions && !extraPayload.length" class="text-sm text-gray-500 mt-4">
              Нет дополнительных параметров
            </p>
          </section>

          <!-- Настройки 3D упражнения (только для 3D типов) -->
          <section v-if="is3DType">
            <h3 class="text-lg font-semibold mb-3">Настройки 3D упражнения</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FieldDisplay label="Количество шариков" :value="template.ball_count || '—'"/>
              <FieldDisplay label="Размер шарика" :value="template.ball_size || '—'"/>
              <FieldDisplay label="Требуемая точность (%)" :value="template.target_accuracy_percent ? `${template.target_accuracy_percent}%` : '—'"/>
              <FieldDisplay label="Скорость" :value="formatSpeed(template.speed)"/>
              <FieldDisplay label="Вертикальная область" :value="formatArea(template.vertical_area)"/>
              <FieldDisplay label="Горизонтальная область" :value="formatArea(template.horizontal_area)"/>
              <FieldDisplay label="Область расстояний" :value="formatDistanceArea(template.distance_area)"/>
            </div>
          </section>
        </div>

        <div v-else class="text-center text-slate-400 py-6">
          Данные шаблона недоступны
        </div>
      </template>
    </BaseTabs>
  </div>
</template>
