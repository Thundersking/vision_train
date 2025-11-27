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

        async fetchParameters(uuid) {
            if (!uuid) {
                return []
            }

            const response = await exerciseTemplateService.fetchParameters(uuid)
            return response.data?.data ?? response.data ?? []
        },

        async fetchSteps(uuid) {
            if (!uuid) {
                return []
            }

            const response = await exerciseTemplateService.fetchSteps(uuid)
            return response.data?.data ?? response.data ?? []
        },

        async createParameter(templateUuid, payload) {
            try {
                const response = await exerciseTemplateService.createParameter(templateUuid, payload)
                toast.success('Целевой параметр добавлен')
                await this.fetchParameters(templateUuid)
                return response.data?.data ?? response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Не удалось создать параметр'
                throw error
            }
        },

        async updateParameter(parameterUuid, payload, templateUuid = null) {
            try {
                const response = await exerciseTemplateService.updateParameter(parameterUuid, payload)
                toast.success('Целевой параметр обновлен')
                if (templateUuid) {
                    await this.fetchParameters(templateUuid)
                }
                return response.data?.data ?? response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Не удалось обновить параметр'
                throw error
            }
        },

        async deleteParameter(parameterUuid, templateUuid = null) {
            try {
                const response = await exerciseTemplateService.deleteParameter(parameterUuid)
                toast.success('Целевой параметр удален')
                if (templateUuid) {
                    await this.fetchParameters(templateUuid)
                }
                return response.data?.data ?? response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Не удалось удалить параметр'
                throw error
            }
        }
    }
})
