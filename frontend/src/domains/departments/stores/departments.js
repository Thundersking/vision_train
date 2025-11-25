import {defineStore} from 'pinia';
import {departmentService} from "@/domains/departments/services/DepartmentService.js";
import {useToast} from 'vue-toastification';

const toast = useToast();

export const useDepartmentsStore = defineStore('department', {
    state: () => ({
        loading: false,
        error: null,
        data: []
    }),

    getters: {
        resource() {
            return departmentService.resource;
        },
    },

    actions: {
        async index(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const response = await departmentService.index(params);
                this.data = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке офисов';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async create(payload) {
            this.loading = true;
            this.error = null;

            try {
                const response = await departmentService.create(payload);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при создании офиса';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async update(uuid, payload) {
            this.loading = true;
            this.error = null;

            try {
                const response = await departmentService.update(uuid, payload);
                toast.success('Офис успешно обновлен');
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при обновлении офиса';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async delete(uuid) {
            this.loading = true;
            this.error = null;

            try {
                return await departmentService.delete(uuid);
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при удалении офиса';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async show(uuid) {
            this.loading = true;
            this.error = null;

            try {
                const response = await departmentService.show(uuid);
                return response.data.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке офиса';
                throw error;
            } finally {
                this.loading = false;
            }
        }
    }
});
