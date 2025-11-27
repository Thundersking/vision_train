<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { ExerciseTemplate } from '@/domains/exercise-templates/models/ExerciseTemplate.js'
import { useExerciseTemplateStore } from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'
import { DIFFICULTY_OPTIONS } from '@/domains/exercise-templates/constants.js'
import ExerciseTemplatePayloadEditor from '@/domains/exercise-templates/components/ExerciseTemplatePayloadEditor.vue'

const router = useRouter()
const templateStore = useExerciseTemplateStore()
const exerciseTypeStore = useExerciseTypeStore()

const form = ref(new ExerciseTemplate())
const formId = 'exercise-template-form-create'
const isSubmitting = ref(false)
const loadingTypes = ref(false)
const typeOptions = ref([])

const $v = useVuelidate(ExerciseTemplate.validationRules(), form)

const fetchTypeOptions = async () => {
  loadingTypes.value = true
  try {
    typeOptions.value = await exerciseTypeStore.allList()
  } finally {
    loadingTypes.value = false
  }
}

onMounted(fetchTypeOptions)

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await templateStore.create(form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({ name: 'exercise-templates' })
}
</script>

<template>
  <div>
    <TitleBlock title="Создание шаблона" :back-to="{ name: 'exercise-templates' }">
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

    <Card :loading="loadingTypes">
      <BaseForm
        v-if="!loadingTypes"
        :id="formId"
        :submit="handleFormSubmit"
        :validator="$v"
        @success="handleSuccess"
      >
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
            :validation="$v"
          />

          <FormInput
            v-model="form"
            name="title"
            label="Название"
            required
            placeholder="Например, Прогрев сетки"
            :validation="$v"
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

          <FormSwitch
            v-model="form"
            name="is_active"
            label="Активен"
          />
        </div>

        <div class="grid grid-cols-1 gap-6 mt-4">
          <FormTextarea
            v-model="form"
            name="short_description"
            label="Краткое описание"
            placeholder="Что делает этот сценарий"
          />
        </div>

        <ExerciseTemplatePayloadEditor
          v-model="form"
          :validation="$v"
          class="mt-6"
        />
      </BaseForm>
    </Card>
  </div>
</template>
