import { defineStore } from 'pinia'
import { patientService } from '@/domains/patients/services/PatientService.js'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const usePatientStore = defineStore('patients', {
  state: () => ({
    data: [],
    meta: null,
    item: null,
    loading: false,
    error: null
  }),
  getters: {
    resource: () => patientService.resource
  },
  actions: {
    async index(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await patientService.index(params)
        this.data = response.data
        this.meta = response.data?.meta ?? null
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при загрузке пациентов'
        throw error
      } finally {
        this.loading = false
      }
    },

    async show(uuid) {
      this.loading = true
      this.error = null

      try {
        const response = await patientService.show(uuid)
        this.item = response.data?.data ?? response.data
        return this.item
      } catch (error) {
        this.error = error.response?.data?.message || 'Пациент не найден'
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(payload) {
      this.loading = true
      this.error = null

      try {
        const response = await patientService.create(payload)
        toast.success('Пациент успешно создан')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при создании пациента'
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(uuid, payload) {
      this.loading = true
      this.error = null

      try {
        const response = await patientService.update(uuid, payload)
        toast.success('Пациент успешно обновлен')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при обновлении пациента'
        throw error
      } finally {
        this.loading = false
      }
    },

    async destroy(uuid) {
      this.loading = true
      this.error = null

      try {
        const response = await patientService.delete(uuid)
        toast.success('Пациент удален')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при удалении пациента'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
