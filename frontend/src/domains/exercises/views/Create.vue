<script setup>
import {onMounted, ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {Exercise} from '@/domains/exercises/models/Exercise.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import ExerciseDetailsForm from '@/domains/exercises/components/ExerciseDetailsForm.vue'

const router = useRouter()
const exerciseStore = useExerciseStore()
const templateStore = useExerciseTemplateStore()
const {handleError} = useErrorHandler()

const form = ref(new Exercise())
const formId = 'exercise-form-create'
const isSubmitting = ref(false)
const loading = ref(false)
const templateOptions = ref([])

const $v = useVuelidate(Exercise.validationRules(), form)

const fetchTemplates = async () => {
  loading.value = true
  try {
    templateOptions.value = await templateStore.allList()
  } catch (err) {
    handleError(err, 'Ошибка при загрузке шаблонов')
  } finally {
    loading.value = false
  }
}

onMounted(fetchTemplates)

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await exerciseStore.create(form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({name: 'exercises'})
}
</script>

<template>
  <div>
    <TitleBlock title="Создание упражнения" :back-to="{name: 'exercises'}">
      <template #actions>
        <Button
            :form="formId"
            type="submit"
            label="Сохранить"
            icon="pi pi-check"
            :loading="isSubmitting"
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
        <ExerciseDetailsForm
            v-model="form"
            :validation="$v"
            :template-options="templateOptions"
        />
      </BaseForm>
    </Card>
  </div>
</template>

