<script setup>
import {onMounted, ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {ExerciseTemplate} from '@/domains/exercise-templates/models/ExerciseTemplate.js'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import ExerciseTemplateDetailsForm from '@/domains/exercise-templates/components/ExerciseTemplateDetailsForm.vue'

const router = useRouter()
const templateStore = useExerciseTemplateStore()

const form = ref(new ExerciseTemplate())
const formId = 'exercise-template-form-create'
const isSubmitting = ref(false)
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

onMounted(fetchLookups)

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
        <ExerciseTemplateDetailsForm
          v-model="form"
          :validation="$v"
          :type-options="typeOptions"
        />
      </BaseForm>
    </Card>
  </div>
</template>
