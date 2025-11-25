import {defineStore} from 'pinia'
import {organizationService} from '@/domains/organization/services/OrganizationService.js'
import {useToast} from 'vue-toastification'

const toast = useToast()

export const useOrganizationStore = defineStore('organization', {
  state: () => ({
    data: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetch() {
      this.loading = true
      this.error = null

      try {
        const response = await organizationService.fetch()
        this.data = response.data.data
        return this.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при загрузке организации'
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(payload) {
      this.loading = true
      this.error = null

      try {
        const response = await organizationService.update(payload)
        this.data = response.data.data
        toast.success('Организация успешно обновлена')
        return this.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при обновлении организации'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
})
