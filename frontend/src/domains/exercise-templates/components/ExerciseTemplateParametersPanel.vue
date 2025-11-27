<script setup>
import {computed, ref, watch} from 'vue'
import {useVuelidate} from '@vuelidate/core'
import {useExerciseTemplateStore} from '@/domains/exercise-templates/stores/exerciseTemplate.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import {useReferenceStore} from '@/common/stores/reference.js'
import {ExerciseTemplateParameter} from '@/domains/exercise-templates/models/ExerciseTemplateParameter.js'

const props = defineProps({
  templateUuid: {
    type: String,
    required: true
  },
  active: {
    type: Boolean,
    default: false
  }
})

const store = useExerciseTemplateStore()
const referenceStore = useReferenceStore()
const {handleError} = useErrorHandler()

const parameters = ref([])
const loading = ref(false)
const loaded = ref(false)
const dialogVisible = ref(false)
const editingParameter = ref(null)
const submitting = ref(false)
const formId = 'exercise-template-parameter-form'
const form = ref(new ExerciseTemplateParameter())
const $v = useVuelidate(ExerciseTemplateParameter.validationRules(), form)

const hasParameters = computed(() => parameters.value.length > 0)
const unitOptions = computed(() => referenceStore.units)
const dialogTitle = computed(() => editingParameter.value ? 'Редактировать параметр' : 'Добавить параметр')

const loadParameters = async (force = false) => {
  if (!props.templateUuid) {
    return
  }

  if (loaded.value && !force) {
    return
  }

  loading.value = true
  try {
    const data = await store.fetchParameters(props.templateUuid)
    parameters.value = Array.isArray(data) ? data : []
    loaded.value = true
  } catch (error) {
    handleError(error, 'Не удалось загрузить параметры')
  } finally {
    loading.value = false
  }
}

const resetState = () => {
  loaded.value = false
  parameters.value = []
}

const resetForm = () => {
  form.value = new ExerciseTemplateParameter()
  editingParameter.value = null
  $v.value.$reset()
}

const ensureUnitsLoaded = async () => {
  if (!referenceStore.unitsLoaded) {
    await referenceStore.fetchUnits()
  }
}

const openCreateDialog = async () => {
  if (!props.templateUuid) {
    return
  }
  await ensureUnitsLoaded()
  resetForm()
  dialogVisible.value = true
}

const openEditDialog = async (parameter) => {
  if (!props.templateUuid) {
    return
  }
  await ensureUnitsLoaded()
  editingParameter.value = parameter
  form.value = new ExerciseTemplateParameter(parameter)
  $v.value.$reset()
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  resetForm()
}

const handleParameterSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Заполните обязательные поля')
  }

  submitting.value = true
  try {
    const payload = form.value.toApiFormat()

    if (editingParameter.value?.uuid) {
      return await store.updateParameter(editingParameter.value.uuid, payload, props.templateUuid)
    }

    return await store.createParameter(props.templateUuid, payload)
  } finally {
    submitting.value = false
  }
}

const handleParameterSuccess = async () => {
  await loadParameters(true)
  closeDialog()
}

const formatValue = (parameter) => {
  const goal = parameter.target_value ?? '—'
  const unit = parameter.unit ? ` ${parameter.unit}` : ''
  return `Цель: ${goal}${unit}`
}

watch(
    () => props.active,
    (isActive) => {
      if (isActive && !loaded.value) {
        loadParameters()
      }

      if (!isActive && dialogVisible.value) {
        closeDialog()
      }
    },
    {immediate: true}
)

watch(() => props.templateUuid, (uuid, prev) => {
  if (!uuid || uuid === prev) {
    return
  }

  closeDialog()
  resetState()
  if (props.active) {
    loadParameters(true)
  }
})
</script>

<template>
  <div>
    <div class="space-y-4">
      <div class="flex justify-end">
        <Button
            label="Добавить параметр"
            icon="pi pi-plus"
            :disabled="!templateUuid"
            @click="openCreateDialog"
        />
      </div>

      <div v-if="loaded && hasParameters" class="space-y-3">
        <div
            v-for="parameter in parameters"
            :key="parameter.uuid || parameter.id || parameter.key"
            class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 flex items-start justify-between gap-4"
        >
          <div>
            <p class="text-sm text-slate-500">{{ parameter.label || parameter.key || 'Параметр' }}</p>
            <p class="font-medium text-slate-900 dark:text-slate-50">{{ formatValue(parameter) }}</p>
          </div>
          <Button
              icon="pi pi-pencil"
              text
              rounded
              severity="secondary"
              @click="openEditDialog(parameter)"
          />
        </div>
      </div>
      <div v-else-if="loaded" class="text-center text-slate-400 py-6">
        Целевые параметры не заданы
      </div>
      <div v-else class="text-center text-slate-400 py-6">
        {{ active ? 'Загрузка параметров...' : 'Выберите вкладку, чтобы загрузить данные' }}
      </div>
    </div>

    <Dialog
      v-model:visible="dialogVisible"
      modal
      :header="dialogTitle"
      :style="{ width: '520px' }"
      :breakpoints="{ '960px': '90vw', '640px': '95vw' }"
      @hide="closeDialog"
  >
    <BaseForm
        :id="formId"
        :submit="handleParameterSubmit"
        :validator="$v"
        @success="handleParameterSuccess"
    >
      <FormInput
          v-model="form"
          name="label"
          label="Название"
          required
          placeholder="Например, Амплитуда"
          :validation="$v"
      />

      <FormInput
          v-model="form"
          name="target_value"
          label="Целевое значение"
          placeholder="Например, 45-60"
          :validation="$v"
      />

      <FormSelect
          v-model="form"
          name="unit"
          label="Единицы измерения"
          :options="unitOptions"
          optionLabel="name"
          optionValue="id"
          placeholder="Выберите значение"
          :loading="referenceStore.unitsLoading"
      />
    </BaseForm>

    <template #footer>
      <div class="flex justify-end gap-3">
        <Button
          type="button"
          label="Отмена"
          severity="secondary"
          :disabled="submitting"
          @click="closeDialog"
        />
        <Button
          type="submit"
          :form="formId"
          label="Сохранить"
          icon="pi pi-check"
          :loading="submitting"
        />
      </div>
    </template>
    </Dialog>
  </div>
</template>
