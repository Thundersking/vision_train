<script setup>
import {computed} from 'vue'
import {EXERCISE_TYPE_OPTIONS, VERTICAL_AREA_OPTIONS, HORIZONTAL_AREA_OPTIONS, DISTANCE_AREA_OPTIONS, SPEED_OPTIONS} from '@/domains/exercises/constants/constants.js'

const props = defineProps({
  validation: {
    type: Object,
    default: null
  },
  patientOptions: {
    type: Array,
    default: () => []
  },
  templateOptions: {
    type: Array,
    default: () => []
  }
})

const form = defineModel({
  type: Object,
  required: true
})

const is3DType = computed(() => {
  return form.value.exercise_type === '3d'
})
</script>

<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <FormSelect
        v-model="form"
        name="patient_id"
        label="Пациент"
        :options="patientOptions"
        optionLabel="full_name"
        optionValue="id"
        placeholder="Выберите пациента"
        required
        :validation="validation"
      />

      <FormSelect
        v-model="form"
        name="exercise_type"
        label="Тип упражнения"
        :options="EXERCISE_TYPE_OPTIONS"
        optionLabel="label"
        optionValue="value"
        placeholder="Выберите тип"
        required
        :validation="validation"
      />

      <FormSelect
        v-model="form"
        name="exercise_template_id"
        label="Шаблон упражнения"
        :options="templateOptions"
        optionLabel="title"
        optionValue="id"
        placeholder="Выберите шаблон (необязательно)"
        :validation="validation"
      />

      <FormInput
        v-model="form"
        name="duration_seconds"
        type="number"
        min="0"
        label="Длительность (сек.)"
        placeholder="Введите длительность"
        :validation="validation"
      />
    </div>

    <!-- Настройки 3D упражнения (только для 3D типов) -->
    <div v-if="is3DType" class="space-y-6 border-t border-slate-200 dark:border-slate-700 pt-6 mt-6">
      <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-50">Настройки 3D упражнения</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <FormInput
          v-model="form"
          name="ball_count"
          type="number"
          min="1"
          max="50"
          label="Количество шариков"
          placeholder="1-50"
          :validation="validation"
        />

        <FormInput
          v-model="form"
          name="ball_size"
          type="number"
          min="1"
          max="10"
          label="Размер шарика"
          placeholder="1-10"
          :validation="validation"
        />

        <FormInput
          v-model="form"
          name="target_accuracy_percent"
          type="number"
          min="1"
          max="100"
          label="Требуемая точность (%)"
          placeholder="1-100"
          :validation="validation"
        />

        <FormSelect
          v-model="form"
          name="speed"
          label="Скорость"
          :options="SPEED_OPTIONS"
          optionLabel="label"
          optionValue="value"
          placeholder="Выберите скорость"
          :validation="validation"
        />

        <FormSelect
          v-model="form"
          name="vertical_area"
          label="Вертикальная область"
          :options="VERTICAL_AREA_OPTIONS"
          optionLabel="label"
          optionValue="value"
          placeholder="Выберите область"
          :validation="validation"
        />

        <FormSelect
          v-model="form"
          name="horizontal_area"
          label="Горизонтальная область"
          :options="HORIZONTAL_AREA_OPTIONS"
          optionLabel="label"
          optionValue="value"
          placeholder="Выберите область"
          :validation="validation"
        />

        <FormSelect
          v-model="form"
          name="distance_area"
          label="Область расстояний"
          :options="DISTANCE_AREA_OPTIONS"
          optionLabel="label"
          optionValue="value"
          placeholder="Выберите область"
          :validation="validation"
        />
      </div>
    </div>
  </div>
</template>

