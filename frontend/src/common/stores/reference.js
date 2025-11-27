import { defineStore } from 'pinia'
import { referenceService } from '@/common/services/ReferenceService.js'

export const useReferenceStore = defineStore('reference', {
  state: () => ({
    units: [],
    unitsLoading: false,
    unitsLoaded: false
  }),
  actions: {
    async fetchUnits(force = false) {
      if (this.unitsLoaded && !force) {
        return this.units
      }

      this.unitsLoading = true
      try {
        const response = await referenceService.getUnits()
        this.units = response.data?.data ?? []
        this.unitsLoaded = true
        return this.units
      } finally {
        this.unitsLoading = false
      }
    }
  }
})
