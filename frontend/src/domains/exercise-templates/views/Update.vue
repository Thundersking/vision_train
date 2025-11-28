<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {ExerciseTemplate} from '@/domains/exercise-templates/models/ExerciseTemplate.js'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import ExerciseTemplateDetailsForm from '@/domains/exercise-templates/components/ExerciseTemplateDetailsForm.vue'

const route = useRoute()
const router = useRouter()
const templateStore = useExerciseTemplateStore()
const { handleError } = useErrorHandler()

const form = ref(new ExerciseTemplate())
const formId = 'exercise-template-form-update'
const isSubmitting = ref(false)
const loading = ref(false)
const loadingTypes = ref(false)
const typeOptions = ref([])

const $v = useVuelidate(ExerciseTemplate.validationRules(), form)

const fetchLookups = async () => {
  loadingTypes.value = true
  try {
    // TODO: вернуть тип (константы)
    // typeOptions.value = await exerciseTypeStore.allList()
  } finally {
    loadingTypes.value = false
  }
}

const loadTemplate = async () => {
  if (!route.params.uuid) {
    return
  }

  loading.value = true
  try {
    const templateData = await templateStore.show(route.params.uuid)
    form.value = new ExerciseTemplate(templateData)
  } catch (err) {
    handleError(err, 'Ошибка при загрузке шаблона')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchLookups(), loadTemplate()])
})

const handleFormSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await templateStore.update(route.params.uuid, form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({ name: 'exercise-template-show', params: { uuid: route.params.uuid } })
}
</script>

<template>
  <div>
    <TitleBlock title="Редактирование шаблона" :back-to="{ name: 'exercise-template-show', params: { uuid: route.params.uuid } }">
      <template #actions>
        <Button
          :form="formId"
          type="submit"
          label="Сохранить"
          icon="pi pi-check"
          :loading="isSubmitting"
          :disabled="loading || loadingTypes"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading || loadingTypes">
      <BaseForm
        v-if="!loading && !loadingTypes"
        :id="formId"
        :submit="handleFormSubmit"
        :validator="$v"
        @success="handleSuccess"
      >
        <ExerciseTemplateDetailsForm
          v-model="form"
          :validation="$v"
          :type-options="typeOptions"
        />
      </BaseForm>
    </Card>
  </div>
</template>

