<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { ExerciseType } from '@/domains/exercise-types/models/ExerciseType.js'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'
import { EXERCISE_DIMENSIONS } from '@/domains/exercise-types/constants.js'

const route = useRoute()
const router = useRouter()
const store = useExerciseTypeStore()

const form = ref(new ExerciseType())
const formId = 'exercise-type-form-update'
const isSubmitting = ref(false)
const loading = ref(true)
const $v = useVuelidate(ExerciseType.validationRules(), form)

const loadData = async () => {
  loading.value = true
  try {
    const data = await store.show(route.params.uuid)
    form.value = new ExerciseType(data)
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await store.update(route.params.uuid, form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({ name: 'exercise-type-show', params: { uuid: route.params.uuid } })
}
</script>

<template>
  <div>
    <TitleBlock
      title="Редактирование типа упражнения"
      :back-to="{ name: 'exercise-type-show', params: { uuid: route.params.uuid } }"
    >
      <template #actions>
        <Button
          :form="formId"
          type="submit"
          label="Сохранить"
          icon="pi pi-check"
          :loading="isSubmitting"
          :disabled="loading"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <BaseForm
        v-if="!loading"
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
            :validation="$v"
          />

          <FormInput
            v-model="form"
            name="slug"
            label="Слаг"
            required
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
          />

          <FormTextarea
            v-model="form"
            name="metrics_json"
            label="Метрики (JSON)"
            :validation="$v"
          />
        </div>
      </BaseForm>
    </Card>
  </div>
</template>
