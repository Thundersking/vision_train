<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { ExerciseType } from '@/domains/exercise-types/models/ExerciseType.js'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'
import { EXERCISE_DIMENSIONS } from '@/domains/exercise-types/constants.js'

const router = useRouter()
const store = useExerciseTypeStore()

const form = ref(new ExerciseType())
const formId = 'exercise-type-form-create'
const isSubmitting = ref(false)
const $v = useVuelidate(ExerciseType.validationRules(), form)

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await store.create(form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({ name: 'exercise-types' })
}
</script>

<template>
  <div>
    <TitleBlock title="Создание типа упражнения" :back-to="{ name: 'exercise-types' }">
      <template #actions>
        <Button
          :form="formId"
          type="submit"
          label="Создать"
          icon="pi pi-check"
          :loading="isSubmitting"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseForm
        :id="formId"
        :submit="handleFormSubmit"
        :validator="$v"
        @success="handleSuccess"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormInput
            v-model="form"
            name="name"
            label="Название"
            required
            placeholder="Например, Тренажёр сетки"
            :validation="$v"
          />

          <FormInput
            v-model="form"
            name="slug"
            label="Слаг"
            required
            placeholder="trenazher-setki"
            :validation="$v"
          />

          <FormSelect
            v-model="form"
            name="dimension"
            label="Формат"
            :options="EXERCISE_DIMENSIONS"
            optionLabel="label"
            optionValue="value"
            required
            :validation="$v"
          />

          <FormSwitch
            v-model="form"
            name="is_customizable"
            label="Можно настраивать"
          />

          <FormSwitch
            v-model="form"
            name="is_active"
            label="Активен"
          />
        </div>

        <div class="grid grid-cols-1 gap-6 mt-4">
          <FormTextarea
            v-model="form"
            name="description"
            label="Описание"
            placeholder="Кратко опишите назначение упражнения"
          />

          <FormTextarea
            v-model="form"
            name="metrics_json"
            label="Метрики (JSON)"
            placeholder='{"metrics":[{"key":"range","label":"Диапазон (°)","unit":"deg"}]}'
            :validation="$v"
          />
        </div>
      </BaseForm>
    </Card>
  </div>
</template>
