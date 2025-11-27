<script setup>
import { computed } from 'vue'
import { ExerciseTemplate } from '@/domains/exercise-templates/models/ExerciseTemplate.js'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true
  },
  validation: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['update:modelValue'])

const form = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const stepsError = computed(() => props.validation?.steps?.$errors?.[0]?.$message ?? null)

const touchSteps = () => {
  props.validation?.steps?.$touch?.()
}

const emitChange = () => {
  emit('update:modelValue', form.value)
}

const addParameter = () => {
  form.value.parameters.push(ExerciseTemplate.createEmptyParameter())
  emitChange()
}

const removeParameter = (index) => {
  form.value.parameters.splice(index, 1)
  emitChange()
}

const addStep = () => {
  form.value.steps.push(ExerciseTemplate.createEmptyStep())
  touchSteps()
  emitChange()
}

const removeStep = (index) => {
  if (form.value.steps.length === 1) {
    form.value.steps.splice(index, 1, ExerciseTemplate.createEmptyStep())
  } else {
    form.value.steps.splice(index, 1)
  }
  touchSteps()
  emitChange()
}
</script>

<template>
  <div class="space-y-6">
    <section class="border border-surface-200 dark:border-surface-700 rounded-lg p-5 space-y-4">
      <div class="flex items-center justify-between gap-2">
        <h3 class="text-lg font-semibold">Общие параметры</h3>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FormInput
          v-model="form"
          name="duration_seconds"
          type="number"
          label="Общая длительность (сек.)"
          placeholder="Например, 300 (5 минут)"
          required
          :validation="validation"
        />
        <FormTextarea
          v-model="form"
          name="instructions"
          label="Общие инструкции для врача"
          placeholder="Например, объясните пациенту принцип упражнения"
          :validation="validation"
        />
      </div>
    </section>

    <section class="border border-surface-200 dark:border-surface-700 rounded-lg p-5 space-y-4">
      <div class="flex items-center justify-between gap-2">
        <div>
          <h3 class="text-lg font-semibold">Целевые параметры</h3>
          <p class="text-sm text-muted-color">
            Опционально: задайте диапазоны или значения, на которые нужно ориентироваться
          </p>
        </div>
        <Button
          icon="pi pi-plus"
          label="Добавить параметр"
          text
          size="small"
          @click="addParameter"
        />
      </div>
      <div v-if="form.parameters.length" class="space-y-4">
        <div
          v-for="(parameter, index) in form.parameters"
          :key="parameter.uid"
          class="border border-surface-200 dark:border-surface-700 rounded-lg p-4"
        >
          <div class="flex items-center justify-between mb-4">
            <h4 class="font-medium">Параметр {{ index + 1 }}</h4>
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              size="small"
              @click="removeParameter(index)"
            />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
              :model-value="parameter"
              name="label"
              label="Отображаемое название"
              placeholder="Например, Амплитуда сетки"
            />
            <FormInput
              :model-value="parameter"
              name="key"
              label="Системный ключ"
              placeholder="Например, amp_range"
            />
            <FormInput
              :model-value="parameter"
              name="target_value"
              label="Целевое значение"
              placeholder="Например, 45-60"
            />
            <FormInput
              :model-value="parameter"
              name="unit"
              label="Единицы измерения"
              placeholder="Например, deg"
            />
          </div>
        </div>
      </div>
      <p v-else class="text-sm text-muted-color">Пока нет параметров — добавьте при необходимости</p>
    </section>

    <section class="border border-surface-200 dark:border-surface-700 rounded-lg p-5 space-y-4">
      <div class="flex items-center justify-between gap-2">
        <div>
          <h3 class="text-lg font-semibold">Шаги сценария</h3>
          <p class="text-sm text-muted-color">Опишите последовательность действий пациента</p>
        </div>
        <Button icon="pi pi-plus" label="Добавить шаг" size="small" text @click="addStep" />
      </div>
      <div v-if="form.steps.length" class="space-y-4">
        <div
          v-for="(step, index) in form.steps"
          :key="step.uid"
          class="border border-surface-200 dark:border-surface-700 rounded-lg p-4 space-y-4"
        >
          <div class="flex items-center justify-between">
            <div class="font-semibold">Шаг {{ index + 1 }}</div>
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              size="small"
              @click="removeStep(index)"
            />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <FormInput
              :model-value="step"
              name="title"
              label="Название шага"
              placeholder="Например, Разогрев"
            />
            <FormInput
              :model-value="step"
              name="duration"
              type="number"
              label="Длительность, сек"
              placeholder="60"
            />
          </div>
          <FormTextarea
            :model-value="step"
            name="description"
            label="Описание/подсказка"
            placeholder="Что пациент делает на этом шаге"
          />
          <FormTextarea
            :model-value="step"
            name="hint"
            label="Комментарий для инструктора (опционально)"
            placeholder="Дополнительные примечания"
          />
        </div>
      </div>
      <p v-else class="text-sm text-muted-color">Шаги ещё не добавлены</p>
      <small v-if="stepsError" class="text-red-500 block">{{ stepsError }}</small>
    </section>
  </div>
</template>
