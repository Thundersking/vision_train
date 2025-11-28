<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {Exercise} from '@/domains/exercises/models/Exercise.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import ExerciseDetailsForm from '@/domains/exercises/components/ExerciseDetailsForm.vue'

const route = useRoute()
const router = useRouter()
const exerciseStore = useExerciseStore()
const templateStore = useExerciseTemplateStore()
const {handleError} = useErrorHandler()

const form = ref(new Exercise())
const formId = 'exercise-form-update'
const isSubmitting = ref(false)
const loading = ref(false)
const templateOptions = ref([])

const $v = useVuelidate(Exercise.validationRules(), form)

const fetchTemplates = async () => {
  try {
    templateOptions.value = await templateStore.allList()
  } catch (err) {
    handleError(err, 'Ошибка при загрузке шаблонов')
  }
}

const loadExercise = async () => {
  if (!route.params.uuid) {
    return
  }

  loading.value = true
  try {
    const exerciseData = await exerciseStore.show(route.params.uuid)
    form.value = new Exercise(exerciseData)
  } catch (err) {
    handleError(err, 'Ошибка при загрузке упражнения')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchTemplates(), loadExercise()])
})

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await exerciseStore.update(route.params.uuid, form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({name: 'exercise-show', params: {uuid: route.params.uuid}})
}
</script>

<template>
  <div>
    <TitleBlock title="Редактирование упражнения" :back-to="{name: 'exercise-show', params: {uuid: route.params.uuid}}">
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
        <ExerciseDetailsForm
            v-model="form"
            :validation="$v"
            :template-options="templateOptions"
        />
      </BaseForm>
    </Card>
  </div>
</template>

