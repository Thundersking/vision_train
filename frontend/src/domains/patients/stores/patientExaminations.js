import { defineStore } from 'pinia'
import { patientExaminationService } from '@/domains/patients/services/PatientExaminationService.js'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const usePatientExaminationsStore = defineStore('patientExaminations', {
  state: () => ({
    items: [],
    loading: false,
    error: null,
    meta: null
  }),
  actions: {
    async fetch(patientUuid, params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await patientExaminationService.index(patientUuid, params)
        this.items = response.data?.data ?? response.data
        this.meta = response.data?.meta ?? null
        return this.items
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при загрузке обследований'
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(patientUuid, payload) {
      this.loading = true
      this.error = null

      try {
        const response = await patientExaminationService.create(patientUuid, payload)
        toast.success('Обследование добавлено')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при добавлении обследования'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
