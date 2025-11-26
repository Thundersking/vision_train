import { defineStore } from 'pinia'
import { patientDeviceService } from '@/domains/patients/services/PatientDeviceService.js'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const usePatientDevicesStore = defineStore('patientDevices', {
  state: () => ({
    items: [],
    activeToken: null,
    loading: false,
    tokenLoading: false,
    error: null
  }),
  actions: {
    reset() {
      this.items = []
      this.activeToken = null
      this.loading = false
      this.tokenLoading = false
      this.error = null
    },

    async fetch(patientUuid) {
      if (!patientUuid) return

      this.loading = true
      this.error = null
      try {
        const response = await patientDeviceService.index(patientUuid)
        const payload = response.data ?? {}
        this.items = payload.data ?? []
        this.activeToken = payload.active_token ?? null
        return payload
      } catch (error) {
        this.error = error.response?.data?.message || 'Не удалось загрузить устройства'
        throw error
      } finally {
        this.loading = false
      }
    },

    async generateToken(patientUuid, payload = {}) {
      if (!patientUuid) return null

      this.tokenLoading = true
      this.error = null
      try {
        const response = await patientDeviceService.generateToken(patientUuid, payload)
        this.activeToken = response.data ?? response
        toast.success('QR-код успешно сгенерирован')
        return this.activeToken
      } catch (error) {
        this.error = error.response?.data?.message || 'Не удалось сгенерировать токен'
        throw error
      } finally {
        this.tokenLoading = false
      }
    },

    async detach(patientUuid, deviceUuid) {
      if (!patientUuid || !deviceUuid) return

      this.loading = true
      this.error = null
      try {
        await patientDeviceService.delete(patientUuid, deviceUuid)
        toast.success('Устройство отвязано')
        this.items = this.items.filter(item => item.device?.uuid !== deviceUuid)
      } catch (error) {
        this.error = error.response?.data?.message || 'Не удалось отвязать устройство'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
