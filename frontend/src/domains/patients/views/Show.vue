<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePatientStore } from '@/domains/patients/stores/patient.js'
import { usePatientExaminationsStore } from '@/domains/patients/stores/patientExaminations.js'
import { Patient } from '@/domains/patients/models/Patient.js'
import { PatientExamination } from '@/domains/patients/models/PatientExamination.js'
import { useErrorHandler } from '@/common/composables/useErrorHandler.js'
import { useDeleteConfirmation } from '@/common/composables/useDeleteConfirmation.js'
import { useTabState } from '@/common/composables/useTabState.js'
import { PATIENT_TABS, EXAMINATION_TYPE_OPTIONS } from '@/domains/patients/constants.js'
import { formatDate, formatDateTime } from '@/common/utils/date.js'
import { useVuelidate } from '@vuelidate/core'

const route = useRoute()
const router = useRouter()
const patientStore = usePatientStore()
const examinationsStore = usePatientExaminationsStore()
const { handleError } = useErrorHandler()
const { confirmDelete } = useDeleteConfirmation()

const patient = ref(null)
const loading = ref(false)
const tabs = PATIENT_TABS
const activeTab = ref('overview')
useTabState(activeTab)

const loadedTabs = reactive({
  examinations: false
})

const examinations = computed(() => examinationsStore.items)
const examinationsLoading = computed(() => examinationsStore.loading)

const examinationFormId = 'patient-examination-form'
const examinationForm = ref(new PatientExamination())
const examinationSubmitting = ref(false)
const examinationValidator = useVuelidate(PatientExamination.validationRules(), examinationForm)

const examinationTypeOptions = EXAMINATION_TYPE_OPTIONS

const fetchPatient = async () => {
  loading.value = true
  try {
    const data = await patientStore.show(route.params.uuid)
    patient.value = new Patient(data)
  } catch (error) {
    handleError(error, 'Пациент не найден')
    router.push({ name: 'patients' })
  } finally {
    loading.value = false
  }
}

const fetchExaminations = async () => {
  try {
    await examinationsStore.fetch(route.params.uuid)
  } catch (error) {
    handleError(error, 'Не удалось загрузить обследования')
  }
}

const handleDelete = async () => {
  if (!patient.value) return
  await confirmDelete({
    deleteFn: () => patientStore.destroy(route.params.uuid),
    entityName: 'пациента',
    entityTitle: patient.value.full_name,
    onSuccess: () => router.push({ name: 'patients' })
  })
}

const handleExamSubmit = async () => {
  examinationValidator.value.$touch()
  if (examinationValidator.value.$invalid) {
    throw new Error('Заполните данные обследования')
  }

  examinationSubmitting.value = true
  try {
    const payload = examinationForm.value.toApiFormat()
    const data = await examinationsStore.create(route.params.uuid, payload)
    return data
  } finally {
    examinationSubmitting.value = false
  }
}

const handleExamSuccess = async () => {
  const preservedType = examinationForm.value.type
  examinationForm.value = new PatientExamination({ type: preservedType })
  examinationValidator.value.$reset?.()
  await fetchExaminations()
}

watch(activeTab, (tab) => {
  if (tab === 'examinations' && !loadedTabs.examinations) {
    loadedTabs.examinations = true
    fetchExaminations()
  }
})

onMounted(() => {
  fetchPatient()
})

watch(
  () => route.params.uuid,
  (uuid, prev) => {
    if (!uuid || uuid === prev) return
    loadedTabs.examinations = false
    examinationsStore.items = []
    fetchPatient()
    if (activeTab.value === 'examinations') {
      loadedTabs.examinations = true
      fetchExaminations()
    }
  }
)

const statusTagSeverity = computed(() => patient.value?.is_active ? 'success' : 'danger')
</script>

<template>
  <div class="space-y-6">
    <TitleBlock
      :title="patient?.full_name || 'Карточка пациента'"
      :description="patient?.email || '—'"
      :back-to="{ name: 'patients' }"
    >
      <template #actions>
        <Button
          label="Редактировать"
          icon="pi pi-pencil"
          severity="secondary"
          :disabled="!patient"
          @click="$router.push({ name: 'patient-update', params: { uuid: route.params.uuid } })"
        />
        <Button
          label="Удалить"
          icon="pi pi-trash"
          severity="danger"
          outlined
          :disabled="!patient"
          @click="handleDelete"
        />
      </template>
    </TitleBlock>

    <BaseTabs :tabs="tabs" v-model="activeTab">
      <template #overview>
        <Card :loading="loading">
          <div v-if="patient" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FieldDisplay label="ФИО" :value="patient.full_name" />
              <FieldDisplay label="Телефон" :value="patient.phone" />
              <FieldDisplay label="Email" :value="patient.email || '—'" />
              <FieldDisplay label="Пол" :value="patient.gender_label" />
              <FieldDisplay label="Дата рождения" :value="formatDate(patient.birth_date)" />
              <FieldDisplay label="Размер руки" :value="patient.hand_size_cm ? patient.hand_size_cm + ' см' : '—'" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FieldDisplay
                label="Статус"
                type="tag"
                :value="patient.is_active ? 'Активен' : 'Неактивен'"
                :tag-severity="statusTagSeverity"
              />
              <FieldDisplay
                label="Врач"
                :value="patient.doctor?.full_name || 'Не назначен'"
              />
            </div>

            <div v-if="patient.notes" class="border-t border-slate-100 dark:border-slate-700 pt-4">
              <h3 class="text-base font-semibold mb-2 text-slate-900 dark:text-slate-50">Примечания</h3>
              <p class="text-slate-600 dark:text-slate-300 whitespace-pre-line">{{ patient.notes }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-slate-100 dark:border-slate-700 pt-4">
              <FieldDisplay label="Создан" :value="formatDateTime(patient.created_at)" />
              <FieldDisplay label="Обновлен" :value="formatDateTime(patient.updated_at)" />
            </div>
          </div>
          <div v-else class="text-center text-slate-400">Данные пациента отсутствуют</div>
        </Card>
      </template>

      <template #examinations>
        <div class="space-y-4">
          <Card>
            <h3 class="text-lg font-semibold mb-4">Добавить обследование</h3>
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

              <template #actions>
                <div class="flex justify-end">
                  <Button
                    type="submit"
                    label="Сохранить"
                    icon="pi pi-check"
                    :loading="examinationSubmitting"
                  />
                </div>
              </template>
            </BaseForm>
          </Card>

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
        </div>
      </template>

      <template #devices>
        <Card>
          <div class="text-slate-500 dark:text-slate-400">
            Раздел устройств будет реализован на следующем этапе.
          </div>
        </Card>
      </template>

      <template #programs>
        <Card>
          <div class="text-slate-500 dark:text-slate-400">
            Раздел программ будет доступен после реализации этапа программ реабилитации.
          </div>
        </Card>
      </template>
    </BaseTabs>
  </div>
</template>
