<script setup>
import {computed, ref, watch} from 'vue'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'

const props = defineProps({
  templateUuid: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  }
})

const store = useExerciseTemplateStore()
const {handleError} = useErrorHandler()

const steps = ref([])
const loading = ref(false)
const loaded = ref(false)

const hasSteps = computed(() => steps.value.length > 0)

const loadSteps = async () => {
  if (loaded.value || !props.templateUuid) {
    return
  }

  loading.value = true
  try {
    const data = await store.fetchSteps(props.templateUuid)
    steps.value = Array.isArray(data) ? data : []
    loaded.value = true
  } catch (error) {
    handleError(error, 'Не удалось загрузить шаги')
  } finally {
    loading.value = false
  }
}

const resetState = () => {
  loaded.value = false
  steps.value = []
}

const formatStepValue = (step) => {
  const parts = []

  if (step.duration) {
    parts.push(`Длительность: ${step.duration} сек.`)
  }

  if (step.description) {
    parts.push(step.description)
  }

  return parts.join(' • ') || '—'
}

watch(() => props.active, (isActive) => {
  if (isActive && !loaded.value) {
    loadSteps()
  }
}, {immediate: true})

watch(() => props.templateUuid, (uuid, prev) => {
  if (!uuid || uuid === prev) {
    return
  }

  resetState()
  if (props.active) {
    loadSteps()
  }
})
</script>

<template>
  <div v-if="loaded && hasSteps" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <FieldDisplay
        v-for="(step, index) in steps"
        :key="step.id || `${step.title}-${index}` || index"
        :label="step.title || `Шаг ${index + 1}`"
        :value="formatStepValue(step)"
    />
  </div>
  <div v-else-if="loaded" class="text-center text-slate-400 py-6">
    Шаги сценария не заданы
  </div>
  <div v-else class="text-center text-slate-400 py-6">
    {{ active ? 'Загрузка шагов...' : 'Выберите вкладку, чтобы загрузить данные' }}
  </div>
</template>
