<script setup>
import { computed, ref, watch } from 'vue'
import { usePatientDevicesStore } from '@/domains/patients/stores/patientDevices.js'
import { useDeleteConfirmation } from '@/common/composables/useDeleteConfirmation.js'
import { useErrorHandler } from '@/common/composables/useErrorHandler.js'
import { formatDateTime } from '@/common/utils/date.js'
import VueQrcode from 'vue-qrcode'

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

const devicesStore = usePatientDevicesStore()
const { confirmDelete } = useDeleteConfirmation()
const { handleError } = useErrorHandler()

const qrDialogVisible = ref(false)
const devicesLoaded = ref(false)

const devices = computed(() => devicesStore.items)
const loading = computed(() => devicesStore.loading)
const tokenLoading = computed(() => devicesStore.tokenLoading)
const activeToken = computed(() => devicesStore.activeToken)
const hasActiveToken = computed(() => Boolean(activeToken.value))
const qrPayload = computed(() => activeToken.value?.qr_payload ?? null)

const qrValue = computed(() => qrPayload.value?.endpoint ?? qrPayload.value?.token ?? '')

const canLoad = computed(() => Boolean(props.patientUuid))

const fetchDevices = async () => {
  if (!canLoad.value) return

  try {
    await devicesStore.fetch(props.patientUuid)
    devicesLoaded.value = true
  } catch (error) {
    handleError(error, 'Не удалось загрузить устройства пациента')
  }
}

const handleGenerateToken = async () => {
  try {
    await devicesStore.generateToken(props.patientUuid)
    qrDialogVisible.value = true
  } catch (error) {
    handleError(error, 'Ошибка генерации QR-кода')
  }
}

const handleShowExistingToken = () => {
  if (activeToken.value) {
    qrDialogVisible.value = true
  }
}

const handleDetach = async (assignment) => {
  if (!assignment?.device?.uuid) return

  const success = await confirmDelete({
    deleteFn: () => devicesStore.detach(props.patientUuid, assignment.device.uuid),
    entityName: 'устройство',
    entityTitle: assignment.device.name || assignment.device.serial_number,
  })

  if (success) {
    await fetchDevices()
  }
}

const handleRefresh = async () => {
  await fetchDevices()
}

watch(() => props.active, (isActive) => {
  if (isActive && !devicesLoaded.value) {
    fetchDevices()
  }
}, { immediate: true })

watch(() => props.patientUuid, (uuid, prev) => {
  if (!uuid || uuid === prev) return

  devicesLoaded.value = false
  devicesStore.reset()

  if (props.active) {
    fetchDevices()
  }
})
</script>

<template>
  <div class="space-y-4">
    <div class="flex flex-wrap gap-3 justify-end">
      <Button
        label="Сгенерировать QR"
        icon="pi pi-refresh"
        :loading="tokenLoading"
        :disabled="!patientUuid"
        @click="handleGenerateToken"
      />
      <Button
        label="Обновить"
        icon="pi pi-sync"
        severity="secondary"
        :loading="loading"
        :disabled="!patientUuid"
        @click="handleRefresh"
      />
    </div>

    <Card>
      <template #title>
        Текущее подключение
      </template>
      <template #subtitle>
        Управляйте QR-кодами для подключения устройства пациента
      </template>

      <div v-if="hasActiveToken" class="space-y-2 text-sm">
        <div class="flex items-center justify-between gap-3">
          <div class="flex flex-wrap items-center gap-2">
            <Tag :value="activeToken.status_label || activeToken.status" severity="info" />
            <span class="text-slate-500">
              Истекает: {{ activeToken.expires_at ? formatDateTime(activeToken.expires_at) : '—' }}
            </span>
          </div>
          <Button
            v-tooltip.top="'Показать QR'"
            icon="pi pi-qrcode"
            severity="secondary"
            text
            rounded
            :loading="tokenLoading"
            :disabled="!hasActiveToken"
            @click="handleShowExistingToken"
          />
        </div>
        <p class="text-slate-500">
          Сгенерирован: {{ activeToken.created_at ? formatDateTime(activeToken.created_at) : '—' }}
        </p>
      </div>
      <div v-else class="text-slate-500 text-sm">
        Активного токена подключения нет. Сгенерируйте новый QR-код.
      </div>
    </Card>

    <Card :loading="loading">
      <template v-if="devices && devices.length">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800">
            <tr>
              <th class="px-4 py-2 text-left font-semibold">Название</th>
              <th class="px-4 py-2 text-left font-semibold">Тип</th>
              <th class="px-4 py-2 text-left font-semibold">Серийный номер</th>
              <th class="px-4 py-2 text-left font-semibold">Прошивка</th>
              <th class="px-4 py-2 text-left font-semibold">Назначено</th>
              <th class="px-4 py-2 text-left font-semibold">Активность</th>
              <th class="px-4 py-2 text-left font-semibold">Действия</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="assignment in devices" :key="assignment.id">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ assignment.device?.name || '—' }}</div>
                <p class="text-xs text-slate-500">{{ assignment.notes || 'Без примечаний' }}</p>
              </td>
              <td class="px-4 py-3">
                <Tag :value="assignment.device?.type || '—'" severity="secondary" />
                <div class="text-xs text-slate-500 mt-1">
                  {{ assignment.is_primary ? 'Основное' : 'Дополнительное' }}
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="text-slate-900 dark:text-slate-100">{{ assignment.device?.serial_number || '—' }}</div>
                <div class="text-xs text-slate-500">{{ assignment.device?.manufacturer || '—' }}</div>
              </td>
              <td class="px-4 py-3">
                {{ assignment.device?.firmware_version || '—' }}
              </td>
              <td class="px-4 py-3">
                <div>{{ formatDateTime(assignment.assigned_at) }}</div>
                <div class="text-xs text-slate-500">{{ assignment.assigned_by?.full_name || '—' }}</div>
              </td>
              <td class="px-4 py-3">
                {{ formatDateTime(assignment.device?.last_sync_at) || '—' }}
              </td>
              <td class="px-4 py-3">
                <Button
                  label="Отвязать"
                  icon="pi pi-unlink"
                  severity="danger"
                  text
                  size="small"
                  @click="handleDetach(assignment)"
                />
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </template>
      <div v-else class="text-center text-slate-400">Подключенные устройства отсутствуют</div>
    </Card>

    <Dialog
      v-model:visible="qrDialogVisible"
      modal
      header="QR для подключения"
      :style="{ width: '480px' }"
      :breakpoints="{ '1024px': '80vw', '640px': '95vw' }"
    >
      <div class="flex flex-col items-center gap-3">
        <VueQrcode
          v-if="qrValue"
          :value="qrValue"
          :width="240"
        />
        <p class="text-center text-slate-500 text-sm">
          Отсканируйте QR-код в приложении, чтобы привязать устройство.
        </p>
        <div class="text-xs text-slate-400">
          Действителен до: {{ activeToken?.expires_at ? formatDateTime(activeToken.expires_at) : '—' }}
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end">
          <Button label="Закрыть" icon="pi pi-times" @click="qrDialogVisible = false" />
        </div>
      </template>
    </Dialog>
  </div>
</template>
