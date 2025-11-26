<script setup>
import {computed, onMounted, ref, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {usePatientStore} from '@/domains/patients/stores/patient.js'
import {Patient} from '@/domains/patients/models/Patient.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import {useDeleteConfirmation} from '@/common/composables/useDeleteConfirmation.js'
import {useTabState} from '@/common/composables/useTabState.js'
import {PATIENT_TABS} from '@/domains/patients/constants.js'
import {formatDate, formatDateTime} from '@/common/utils/date.js'
import PatientExaminationsPanel from '@/domains/patients/components/PatientExaminationsPanel.vue'

const route = useRoute()
const router = useRouter()
const patientStore = usePatientStore()
const {handleError} = useErrorHandler()
const {confirmDelete} = useDeleteConfirmation()

const patient = ref(null)
const loading = ref(false)
const tabs = PATIENT_TABS
const activeTab = ref('overview')
useTabState(activeTab)
const isExaminationsTabActive = computed(() => activeTab.value === 'examinations')

const fetchPatient = async () => {
  loading.value = true
  try {
    const data = await patientStore.show(route.params.uuid)
    patient.value = new Patient(data)
  } catch (error) {
    handleError(error, 'Пациент не найден')
    router.push({name: 'patients'})
  } finally {
    loading.value = false
  }
}

const handleDelete = async () => {
  if (!patient.value) return
  await confirmDelete({
    deleteFn: () => patientStore.destroy(route.params.uuid),
    entityName: 'пациента',
    entityTitle: patient.value.full_name,
    onSuccess: () => router.push({name: 'patients'})
  })
}

onMounted(() => {
  fetchPatient()
})

watch(
    () => route.params.uuid,
    (uuid, prev) => {
      if (!uuid || uuid === prev) return
      fetchPatient()
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

    <BaseTabs
        :tabs="tabs"
        v-model="activeTab"
        :loading="{ overview: loading }"
    >
      <template #overview>
        <div v-if="patient" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FieldDisplay label="ФИО" :value="patient.full_name"/>
            <FieldDisplay label="Телефон" :value="patient.phone"/>
            <FieldDisplay label="Email" :value="patient.email || '—'"/>
            <FieldDisplay label="Пол" :value="patient.gender_label"/>
            <FieldDisplay label="Дата рождения" :value="formatDate(patient.birth_date)"/>
            <FieldDisplay label="Размер руки" :value="patient.hand_size_cm ? patient.hand_size_cm + ' см' : '—'"/>
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
            <FieldDisplay label="Создан" :value="formatDateTime(patient.created_at)"/>
            <FieldDisplay label="Обновлен" :value="formatDateTime(patient.updated_at)"/>
          </div>
        </div>
        <div v-else class="text-center text-slate-400">Данные пациента отсутствуют</div>
      </template>

      <template #examinations>
        <PatientExaminationsPanel
            :patient="patient"
            :patient-uuid="route.params.uuid"
            :active="isExaminationsTabActive"
        />
      </template>

      <template #devices>
        <div class="text-slate-500 dark:text-slate-400">
          Раздел устройств будет реализован на следующем этапе.
        </div>
      </template>

      <template #programs>
        <div class="text-slate-500 dark:text-slate-400">
          Раздел программ будет доступен после реализации этапа программ реабилитации.
        </div>
      </template>
    </BaseTabs>
  </div>
</template>
