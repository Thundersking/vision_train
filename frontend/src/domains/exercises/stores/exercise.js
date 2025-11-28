import {defineStore} from 'pinia'
import {useToast} from 'vue-toastification'
import {exerciseService} from '@/domains/exercises/services/ExerciseService.js'

const toast = useToast()

export const useExerciseStore = defineStore('exercise', {
    state: () => ({
        data: [],
        meta: null,
        item: null,
        loading: false,
        error: null
    }),

    getters: {
        resource: () => exerciseService.resource
    },

    actions: {
        async index(params = {}) {
            this.loading = true
            this.error = null

            try {
                const response = await exerciseService.index(params)
                this.data = response.data
                this.meta = response.data?.meta ?? null
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке упражнений'
                throw error
            } finally {
                this.loading = false
            }
        },

        async show(uuid) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseService.show(uuid)
                this.item = response.data?.data ?? response.data
                return this.item
            } catch (error) {
                this.error = error.response?.data?.message || 'Упражнение не найдено'
                throw error
            } finally {
                this.loading = false
            }
        },

        async create(payload) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseService.create(payload)
                toast.success('Упражнение создано')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при создании упражнения'
                throw error
            } finally {
                this.loading = false
            }
        },

        async update(uuid, payload) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseService.update(uuid, payload)
                toast.success('Упражнение обновлено')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при обновлении упражнения'
                throw error
            } finally {
                this.loading = false
            }
        },

        async delete(uuid) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseService.delete(uuid)
                toast.success('Упражнение удалено')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при удалении упражнения'
                throw error
            } finally {
                this.loading = false
            }
        },

        async indexByPatient(patientUuid, params = {}) {
            this.loading = true
            this.error = null

            try {
                const response = await exerciseService.indexByPatient(patientUuid, params)
                this.data = response.data
                this.meta = response.data?.meta ?? null
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке упражнений пациента'
                throw error
            } finally {
                this.loading = false
            }
        }
    }
})

