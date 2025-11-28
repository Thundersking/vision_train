<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useExerciseStore} from '@/domains/exercises/stores/exercise.js'
import {usePatientStore} from '@/domains/patients/stores/patient.js'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {Exercise} from '@/domains/exercises/models/Exercise.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import ExerciseDetailsForm from '@/domains/exercises/components/ExerciseDetailsForm.vue'

const route = useRoute()
const router = useRouter()
const exerciseStore = useExerciseStore()
const patientStore = usePatientStore()
const templateStore = useExerciseTemplateStore()
const {handleError} = useErrorHandler()

const form = ref(new Exercise())
const formId = 'exercise-form-update'
const isSubmitting = ref(false)
const loading = ref(false)
const loadingPatients = ref(false)
const loadingTemplates = ref(false)
const patientOptions = ref([])
const templateOptions = ref([])

const $v = useVuelidate(Exercise.validationRules(), form)

const fetchLookups = async () => {
  loadingPatients.value = true
  loadingTemplates.value = true
  try {
    const [patients, templates] = await Promise.all([
      patientStore.index({per_page: 100}),
      templateStore.index({per_page: 100})
    ])
    patientOptions.value = patients?.data ?? []
    templateOptions.value = templates?.data ?? []
  } catch (err) {
    handleError(err, 'Ошибка при загрузке справочников')
  } finally {
    loadingPatients.value = false
    loadingTemplates.value = false
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
  await Promise.all([fetchLookups(), loadExercise()])
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
            :disabled="loading || loadingPatients || loadingTemplates"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading || loadingPatients || loadingTemplates">
      <BaseForm
          v-if="!loading && !loadingPatients && !loadingTemplates"
          :id="formId"
          :submit="handleFormSubmit"
          :validator="$v"
          @success="handleSuccess"
      >
        <ExerciseDetailsForm
            v-model="form"
            :validation="$v"
            :patient-options="patientOptions"
            :template-options="templateOptions"
        />
      </BaseForm>
    </Card>
  </div>
</template>

