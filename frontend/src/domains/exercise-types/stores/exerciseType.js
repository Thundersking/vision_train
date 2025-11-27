import { defineStore } from 'pinia'
import { useToast } from 'vue-toastification'
import { exerciseTypeService } from '@/domains/exercise-types/services/ExerciseTypeService.js'

const toast = useToast()

export const useExerciseTypeStore = defineStore('exercise-types', {
  state: () => ({
    data: [],
    meta: null,
    item: null,
    loading: false,
    error: null
  }),
  getters: {
    resource: () => exerciseTypeService.resource
  },
  actions: {
    async index(params = {}) {
      this.loading = true
      this.error = null

      try {
        const response = await exerciseTypeService.index(params)
        this.data = response.data
        this.meta = response.data?.meta ?? null
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при загрузке типов упражнений'
        throw error
      } finally {
        this.loading = false
      }
    },

    async show(uuid) {
      this.loading = true
      this.error = null
      try {
        const response = await exerciseTypeService.show(uuid)
        this.item = response.data?.data ?? response.data
        return this.item
      } catch (error) {
        this.error = error.response?.data?.message || 'Тип упражнения не найден'
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(payload) {
      this.loading = true
      this.error = null
      try {
        const response = await exerciseTypeService.create(payload)
        toast.success('Тип упражнения создан')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при создании типа'
        throw error
      } finally {
        this.loading = false
      }
    },

    async update(uuid, payload) {
      this.loading = true
      this.error = null
      try {
        const response = await exerciseTypeService.update(uuid, payload)
        toast.success('Тип упражнения обновлен')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при обновлении типа'
        throw error
      } finally {
        this.loading = false
      }
    },

    async destroy(uuid) {
      this.loading = true
      this.error = null
      try {
        const response = await exerciseTypeService.delete(uuid)
        toast.success('Тип удален')
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Ошибка при удалении типа'
        throw error
      } finally {
        this.loading = false
      }
    },

    async allList(params = {}) {
      const response = await exerciseTypeService.allList(params)
      return response.data?.data ?? []
    }
  }
})
