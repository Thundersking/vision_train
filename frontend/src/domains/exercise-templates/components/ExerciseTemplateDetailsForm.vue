<script setup>
import { DIFFICULTY_OPTIONS } from '@/domains/exercise-templates/constants/constants.js'

defineProps({
  validation: {
    type: Object,
    default: null
  },
  typeOptions: {
    type: Array,
    default: () => []
  }
})

const form = defineModel({
  type: Object,
  required: true
})
</script>

<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <FormSelect
        v-model="form"
        name="exercise_type_id"
        label="Тип упражнения"
        :options="typeOptions"
        optionLabel="name"
        optionValue="id"
        placeholder="Выберите тип"
        required
        :validation="validation"
      />

      <FormInput
        v-model="form"
        name="title"
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
  </div>
</template>
