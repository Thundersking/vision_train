<script setup>
import {computed, onMounted, ref, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {EXERCISE_TABS} from '@/domains/exercises/constants/constants.js'
import {useTabState} from '@/common/composables/useTabState.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import {formatDateTime} from '@/common/utils/date.js'
import ExerciseResultsPanel from '@/domains/exercises/components/ExerciseResultsPanel.vue'
import {TYPE_3D} from "@/domains/exercise-templates/constants/type.js";

const route = useRoute()
const router = useRouter()
const store = useExerciseStore()
const {handleError} = useErrorHandler()

const exercise = ref(null)
const loading = ref(false)
const tabs = EXERCISE_TABS
const activeTab = ref('overview')
useTabState(activeTab)

const exerciseUuid = computed(() => route.params.uuid)
const isResultsTabActive = computed(() => activeTab.value === 'results')

const loadData = async () => {
  if (!exerciseUuid.value) {
    return
  }

  loading.value = true
  try {
    exercise.value = await store.show(exerciseUuid.value)
  } catch (err) {
    handleError(err, 'Ошибка при загрузке упражнения')
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

watch(() => exerciseUuid.value, (uuid, prev) => {
  if (!uuid || uuid === prev) {
    return
  }
  loadData()
})

const formattedDuration = computed(() => {
  const seconds = Number(exercise.value?.duration_seconds)
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
</script>

<template>
  <div class="space-y-6">
    <TitleBlock
        :title="`Упражнение ${exercise?.exercise_type_label || ''}`"
        :back-to="{ name: 'exercises' }"
    >
      <template #actions>
        <Button
            label="Редактировать"
            icon="pi pi-pencil"
            severity="secondary"
            :disabled="!exercise"
            @click="$router.push({ name: 'exercise-update', params: { uuid: exerciseUuid } })"
        />
      </template>
    </TitleBlock>

    <BaseTabs
        v-model="activeTab"
        :tabs="tabs"
        :loading="{ overview: loading }"
    >
      <template #overview>
        <div v-if="exercise" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FieldDisplay label="Тип упражнения" :value="exercise.exercise_type_label"/>
            <FieldDisplay label="Пациент" :value="exercise.patient?.full_name || '—'"/>
            <FieldDisplay label="Шаблон" :value="exercise.template?.title || '—'"/>
            <FieldDisplay label="Длительность" :value="formattedDuration"/>
            <FieldDisplay label="Начало" :value="formatDateTime(exercise.started_at)"/>
            <FieldDisplay label="Завершение" :value="formatDateTime(exercise.completed_at)"/>
          </div>

          <section v-if="exercise.exercise_type === TYPE_3D" class="border-t border-slate-200 dark:border-slate-700 pt-6">
            <h3 class="text-lg font-semibold mb-3">Настройки 3D упражнения</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FieldDisplay label="Количество шариков" :value="exercise.ball_count || '—'"/>
              <FieldDisplay label="Размер шарика" :value="exercise.ball_size || '—'"/>
              <FieldDisplay label="Требуемая точность (%)" :value="exercise.target_accuracy_percent ? `${exercise.target_accuracy_percent}%` : '—'"/>
              <FieldDisplay label="Скорость" :value="exercise.speed || '—'"/>
              <FieldDisplay label="Вертикальная область" :value="exercise.vertical_area || '—'"/>
              <FieldDisplay label="Горизонтальная область" :value="exercise.horizontal_area || '—'"/>
              <FieldDisplay label="Область расстояний" :value="exercise.distance_area || '—'"/>
            </div>
          </section>
        </div>

        <div v-else class="text-center text-slate-400 py-6">
          Данные упражнения недоступны
        </div>
      </template>

      <template #results>
        <ExerciseResultsPanel
            :exercise="exercise"
            :exercise-uuid="exerciseUuid"
            :active="isResultsTabActive"
            @updated="loadData"
        />
      </template>
    </BaseTabs>
  </div>
</template>

