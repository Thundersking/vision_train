<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { ExerciseTemplate } from '@/domains/exercise-templates/models/ExerciseTemplate.js'
import { useExerciseTemplateStore } from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import { useExerciseTypeStore } from '@/domains/exercise-types/stores/exerciseType.js'
import { DIFFICULTY_OPTIONS } from '@/domains/exercise-templates/constants.js'
import ExerciseTemplatePayloadEditor from '@/domains/exercise-templates/components/ExerciseTemplatePayloadEditor.vue'
import { useReferenceStore } from '@/common/stores/reference.js'

const route = useRoute()
const router = useRouter()
const templateStore = useExerciseTemplateStore()
const exerciseTypeStore = useExerciseTypeStore()
const referenceStore = useReferenceStore()

const form = ref(new ExerciseTemplate())
const typeOptions = ref([])
const formId = 'exercise-template-form-update'
const loading = ref(true)
const isSubmitting = ref(false)
const $v = useVuelidate(ExerciseTemplate.validationRules(), form)
const unitOptions = computed(() => referenceStore.units)

const initialize = async () => {
  loading.value = true
  try {
    const [options, template] = await Promise.all([
      exerciseTypeStore.allList(),
      templateStore.show(route.params.uuid),
      referenceStore.fetchUnits()
    ])
    typeOptions.value = options
    form.value = new ExerciseTemplate(template)
  } finally {
    loading.value = false
  }
}

onMounted(initialize)

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
    <TitleBlock
      title="Редактирование шаблона"
      :back-to="{ name: 'exercise-template-show', params: { uuid: route.params.uuid } }"
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
          <FormSelect
            v-model="form"
            name="exercise_type_id"
            label="Тип упражнения"
            :options="typeOptions"
            optionLabel="name"
            optionValue="id"
            required
            :validation="$v"
          />

          <FormInput
            v-model="form"
            name="title"
            label="Название"
            required
            :validation="$v"
          />

          <FormSelect
            v-model="form"
            name="difficulty"
            label="Сложность"
            :options="DIFFICULTY_OPTIONS"
            optionLabel="label"
            optionValue="value"
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
          />
        </div>

        <ExerciseTemplatePayloadEditor
          v-model="form"
          :validation="$v"
          :unit-options="unitOptions"
          class="mt-6"
        />
      </BaseForm>
    </Card>
  </div>
</template>
