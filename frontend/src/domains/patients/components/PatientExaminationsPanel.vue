<script setup>
import { computed, ref, watch } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { usePatientExaminationsStore } from '@/domains/patients/stores/patientExaminations.js'
import { PatientExamination } from '@/domains/patients/models/PatientExamination.js'
import { EXAMINATION_TYPE_OPTIONS } from '@/domains/patients/constants.js'
import { useErrorHandler } from '@/common/composables/useErrorHandler.js'
import { formatDateTime } from '@/common/utils/date.js'

const props = defineProps({
  patientUuid: {
    type: String,
    required: true
  },
  patient: {
    type: Object,
    default: null
  },
  active: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['created'])

const examinationsStore = usePatientExaminationsStore()
const { handleError } = useErrorHandler()

const examinations = computed(() => examinationsStore.items)
const examinationsLoading = computed(() => examinationsStore.loading)

const examinationFormId = 'patient-examination-form'
const examinationForm = ref(new PatientExamination())
const examinationSubmitting = ref(false)
const examinationValidator = useVuelidate(PatientExamination.validationRules(), examinationForm)
const examinationDialogVisible = ref(false)
const examinationsLoaded = ref(false)

const examinationTypeOptions = EXAMINATION_TYPE_OPTIONS

const canLoad = computed(() => Boolean(props.patientUuid))

const resetExaminationForm = (preserveType = false) => {
  const preservedType = preserveType ? examinationForm.value.type : null
  examinationForm.value = preservedType ? new PatientExamination({ type: preservedType }) : new PatientExamination()
  examinationValidator.value.$reset?.()
}

const fetchExaminations = async () => {
  if (!canLoad.value) {
    return
  }

  try {
    await examinationsStore.fetch(props.patientUuid)
    examinationsLoaded.value = true
  } catch (error) {
    handleError(error, 'Не удалось загрузить обследования')
  }
}

const handleExamSubmit = async () => {
  examinationValidator.value.$touch()
  if (examinationValidator.value.$invalid) {
    throw new Error('Заполните данные обследования')
  }

  examinationSubmitting.value = true
  try {
    const payload = examinationForm.value.toApiFormat()
    return await examinationsStore.create(props.patientUuid, payload)
  } finally {
    examinationSubmitting.value = false
  }
}

const handleExamSuccess = async () => {
  await fetchExaminations()
  examinationDialogVisible.value = false
  resetExaminationForm(true)
  emit('created')
}

watch(() => props.active, (isActive) => {
  if (isActive && !examinationsLoaded.value) {
    fetchExaminations()
  }
}, { immediate: true })

watch(() => props.patientUuid, (uuid, prev) => {
  if (!uuid || uuid === prev) {
    return
  }

  examinationsLoaded.value = false
  examinationsStore.items = []
  examinationDialogVisible.value = false
  resetExaminationForm()

  if (props.active) {
    fetchExaminations()
  }
})
</script>

<template>
  <div class="space-y-4">
    <div class="flex justify-end">
      <Button
        label="Добавить обследование"
        icon="pi pi-plus"
        :disabled="!patientUuid"
        @click="examinationDialogVisible = true"
      />
    </div>

    <Card :loading="examinationsLoading">
      <template v-if="examinations && examinations.length">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800">
            <tr>
              <th class="px-4 py-2 text-left font-semibold">Тип</th>
              <th class="px-4 py-2 text-left font-semibold">Дата</th>
              <th class="px-4 py-2 text-left font-semibold">Врач</th>
              <th class="px-4 py-2 text-left font-semibold">Результаты</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="exam in examinations" :key="exam.uuid">
              <td class="px-4 py-3">
                {{ exam.type_label || exam.type }}
              </td>
              <td class="px-4 py-3">
                {{ formatDateTime(exam.examination_date) }}
              </td>
              <td class="px-4 py-3">
                {{ exam.doctor?.full_name || '—' }}
              </td>
              <td class="px-4 py-3">
                <p class="text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ exam.results }}</p>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </template>
      <div v-else class="text-center text-slate-400">Нет записей обследований</div>
    </Card>

    <Dialog
      v-model:visible="examinationDialogVisible"
      modal
      header="Добавить обследование"
      :style="{ width: '720px' }"
      :breakpoints="{ '1024px': '90vw', '640px': '95vw' }"
      @hide="resetExaminationForm()"
    >
      <BaseForm
        :id="examinationFormId"
        :submit="handleExamSubmit"
        :validator="examinationValidator"
        @success="handleExamSuccess"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <FormSelect
            v-model="examinationForm"
            name="type"
            label="Тип"
            :options="examinationTypeOptions"
            required
            placeholder="Выберите тип"
            :validation="examinationValidator"
          />

          <FormDateTime
            v-model="examinationForm"
            name="examination_date"
            label="Дата обследования"
            required
            :validation="examinationValidator"
          />

          <FormInput
            v-model="examinationForm"
            name="title"
            label="Название"
            required
            placeholder="Например, Первичный осмотр"
            :validation="examinationValidator"
          />

          <FormTextarea
            v-model="examinationForm"
            name="description"
            label="Описание"
            rows="3"
            placeholder="Краткое описание обследования"
            :validation="examinationValidator"
          />
        </div>

        <FormTextarea
          v-model="examinationForm"
          name="results"
          label="Результаты"
          required
          rows="4"
          placeholder="Основные результаты обследования"
          :validation="examinationValidator"
        />

        <FormTextarea
          v-model="examinationForm"
          name="recommendations"
          label="Рекомендации"
          rows="3"
          placeholder="Рекомендации пациенту"
          :validation="examinationValidator"
        />
      </BaseForm>

      <template #footer>
        <div class="flex justify-end gap-3">
          <Button
            type="button"
            label="Отмена"
            severity="secondary"
            @click="examinationDialogVisible = false"
          />
          <Button
            type="submit"
            :form="examinationFormId"
            label="Сохранить"
            icon="pi pi-check"
            :loading="examinationSubmitting"
          />
        </div>
      </template>
    </Dialog>
  </div>
</template>
