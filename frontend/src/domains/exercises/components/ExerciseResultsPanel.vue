<script setup>
import {computed, ref, watch} from 'vue'
import {useVuelidate} from '@vuelidate/core'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {Exercise} from '@/domains/exercises/models/Exercise.js'
import {FATIGUE_OPTIONS, PATIENT_DECISION_OPTIONS} from '@/domains/exercises/constants/constants.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'

const props = defineProps({
  exerciseUuid: {
    type: String,
    required: true
  },
  exercise: {
    type: Object,
    default: null
  },
  active: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['updated'])

const exerciseStore = useExerciseStore()
const {handleError} = useErrorHandler()

const resultsFormId = 'exercise-results-form'
const resultsForm = ref(new Exercise())
const resultsSubmitting = ref(false)
const resultsValidator = useVuelidate(Exercise.validationRules(), resultsForm)

const resetResultsForm = () => {
  if (props.exercise) {
    resultsForm.value = new Exercise(props.exercise)
  } else {
    resultsForm.value = new Exercise()
  }
  resultsValidator.value.$reset?.()
}

const handleResultsSubmit = async () => {
  resultsValidator.value.$touch()
  if (resultsValidator.value.$invalid) {
    throw new Error('Заполните данные результатов')
  }

  resultsSubmitting.value = true
  try {
    const payload = resultsForm.value.toApiFormat()
    return await exerciseStore.update(props.exerciseUuid, payload)
  } finally {
    resultsSubmitting.value = false
  }
}

const handleResultsSuccess = async () => {
  await exerciseStore.show(props.exerciseUuid)
  emit('updated')
}

watch(() => props.exercise, (newExercise) => {
  if (newExercise) {
    resetResultsForm()
  }
}, { immediate: true })
</script>

<template>
  <div class="space-y-6">
    <div v-if="exercise" class="space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FieldDisplay label="Усталость правого глаза" :value="exercise.fatigue_right_eye ? `${exercise.fatigue_right_eye}/5` : '—'"/>
        <FieldDisplay label="Усталость левого глаза" :value="exercise.fatigue_left_eye ? `${exercise.fatigue_left_eye}/5` : '—'"/>
        <FieldDisplay label="Усталость головы" :value="exercise.fatigue_head ? `${exercise.fatigue_head}/5` : '—'"/>
        <FieldDisplay label="Решение пациента" :value="exercise.patient_decision === 'continue' ? 'Продолжить' : exercise.patient_decision === 'stop' ? 'Остановить' : '—'"/>
      </div>

      <div v-if="exercise.notes" class="border-t border-slate-100 dark:border-slate-700 pt-4">
        <h3 class="text-base font-semibold mb-2 text-slate-900 dark:text-slate-50">Примечания</h3>
        <p class="text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ exercise.notes }}</p>
      </div>
    </div>

    <div v-else class="text-center text-slate-400">
      Данные результатов недоступны
    </div>

    <div v-if="exercise" class="border-t border-slate-200 dark:border-slate-700 pt-6">
      <h3 class="text-lg font-semibold mb-4">Редактировать результаты</h3>
      
      <BaseForm
          :id="resultsFormId"
          :submit="handleResultsSubmit"
          :validator="resultsValidator"
          @success="handleResultsSuccess"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormSelect
            v-model="resultsForm"
            name="fatigue_right_eye"
            label="Усталость правого глаза"
            :options="FATIGUE_OPTIONS"
            optionLabel="label"
            optionValue="value"
            placeholder="Выберите значение"
            :validation="resultsValidator"
          />

          <FormSelect
            v-model="resultsForm"
            name="fatigue_left_eye"
            label="Усталость левого глаза"
            :options="FATIGUE_OPTIONS"
            optionLabel="label"
            optionValue="value"
            placeholder="Выберите значение"
            :validation="resultsValidator"
          />

          <FormSelect
            v-model="resultsForm"
            name="fatigue_head"
            label="Усталость головы"
            :options="FATIGUE_OPTIONS"
            optionLabel="label"
            optionValue="value"
            placeholder="Выберите значение"
            :validation="resultsValidator"
          />

          <FormSelect
            v-model="resultsForm"
            name="patient_decision"
            label="Решение пациента"
            :options="PATIENT_DECISION_OPTIONS"
            optionLabel="label"
            optionValue="value"
            placeholder="Выберите решение"
            :validation="resultsValidator"
          />

          <div class="md:col-span-2">
            <FormTextarea
              v-model="resultsForm"
              name="notes"
              label="Примечания"
              placeholder="Дополнительные заметки"
              :validation="resultsValidator"
            />
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <Button
            type="submit"
            :form="resultsFormId"
            label="Сохранить"
            icon="pi pi-check"
            :loading="resultsSubmitting"
          />
        </div>
      </BaseForm>
    </div>
  </div>
</template>

