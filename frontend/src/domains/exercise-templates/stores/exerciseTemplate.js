import {defineStore} from 'pinia'
import {useToast} from 'vue-toastification'
import {exerciseTemplateService} from '@/domains/exercise-templates/services/ExerciseTemplateService.js'

const toast = useToast()

export const useExerciseTemplateStore = defineStore('exercise-templates', {
    state: () => ({
        data: [],
        meta: null,
        item: null,
        loading: false,
        error: null
    }),
    getters: {
        resource: () => exerciseTemplateService.resource
    },
    actions: {
        async index(params = {}) {
            this.loading = true
            this.error = null

            try {
                const response = await exerciseTemplateService.index(params)
                this.data = response.data
                this.meta = response.data?.meta ?? null
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке шаблонов'
                throw error
            } finally {
                this.loading = false
            }
        },

        async show(uuid) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseTemplateService.show(uuid)
                this.item = response.data?.data ?? response.data
                return this.item
            } catch (error) {
                this.error = error.response?.data?.message || 'Шаблон не найден'
                throw error
            } finally {
                this.loading = false
            }
        },

        async create(payload) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseTemplateService.create(payload)
                toast.success('Шаблон упражнения создан')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при создании шаблона'
                throw error
            } finally {
                this.loading = false
            }
        },

        async update(uuid, payload) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseTemplateService.update(uuid, payload)
                toast.success('Шаблон упражнения обновлен')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при обновлении шаблона'
                throw error
            } finally {
                this.loading = false
            }
        },

        async destroy(uuid) {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseTemplateService.delete(uuid)
                toast.success('Шаблон удален')
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при удалении шаблона'
                throw error
            } finally {
                this.loading = false
            }
        },

        /**
         * Получить короткий список шаблонов (id, title) для выпадающих списков
         * @returns {Promise<Array>} - массив объектов {id, title}
         */
        async allList() {
            this.loading = true
            this.error = null
            try {
                const response = await exerciseTemplateService.allList()
                return response.data?.data ?? []
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке списка шаблонов'
                throw error
            } finally {
                this.loading = false
            }
        }
    }
})
