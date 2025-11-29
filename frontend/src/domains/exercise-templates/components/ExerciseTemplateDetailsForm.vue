<script setup>
import { computed } from 'vue'
import {EXERCISE_TYPE_OPTIONS, TYPE_3D} from "@/domains/exercise-templates/constants/type.js";
import {DIFFICULTY_OPTIONS} from "@/domains/exercise-templates/constants/difficulty.js";

const props = defineProps({
  validation: {
    type: Object,
    default: null
  }
})

const form = defineModel({
  type: Object,
  required: true
})

const is3DType = computed(() => {
  return form.value.exercise_type === TYPE_3D
})

// Опции для выбора областей и скорости
const VERTICAL_AREA_OPTIONS = [
  { label: 'Полная', value: 'full' },
  { label: 'Верхняя', value: 'top' },
  { label: 'Нижняя', value: 'bottom' }
]

const HORIZONTAL_AREA_OPTIONS = [
  { label: 'Полная', value: 'full' },
  { label: 'Левая', value: 'left' },
  { label: 'Правая', value: 'right' }
]

const DISTANCE_AREA_OPTIONS = [
  { label: 'Полная', value: 'full' },
  { label: 'Близко', value: 'near' },
  { label: 'Средне', value: 'medium' },
  { label: 'Далеко', value: 'far' }
]

const SPEED_OPTIONS = [
  { label: 'Медленно', value: 'slow' },
  { label: 'Средне', value: 'medium' },
  { label: 'Быстро', value: 'fast' }
]
</script>

<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <FormSelect
        v-model="form"
        name="exercise_type"
        label="Тип упражнения"
        :options="EXERCISE_TYPE_OPTIONS"
        placeholder="Выберите тип"
        required
        :validation="validation"
      />

      <FormInput
        v-model="form"
        name="name"
        label="Название"
        placeholder="Например, Тренажёр сетки"
        required
        :validation="validation"
      />

      <FormSelect
        v-model="form"
        name="difficulty"
        label="Сложность"
        :options="DIFFICULTY_OPTIONS"
        optionLabel="label"
        optionValue="value"
        placeholder="Выберите сложность"
      />

      <FormInput
        v-model="form"
        name="duration_seconds"
        type="number"
        min="0"
        label="Общая длительность (сек.)"
        placeholder="Введите длительность сценария"
      />

      <FormSwitch
        v-model="form"
        name="is_active"
        label="Активен"
      />
    </div>

    <div class="grid grid-cols-1 gap-6">
      <FormTextarea
        v-model="form"
        name="short_description"
        label="Краткое описание"
        placeholder="Коротко опишите смысл упражнения"
      />

      <FormTextarea
        v-model="form"
        name="instructions"
        label="Инструкции для врача"
        placeholder="Уточнения, которые необходимо сообщить пациенту"
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
