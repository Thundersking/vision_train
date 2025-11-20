import {defineStore} from 'pinia';
import {userService} from "@/domains/users/services/UserService.js";
import {useToast} from "primevue/usetoast";

export const useUserStore = defineStore('user', {
    state: () => ({
        loading: false,
        error: null,
        data: []
    }),

    getters: {
        resource() {
            return userService.resource;
        }
    },

    actions: {
        async index(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const response = await userService.index(params);
                this.data = response.data
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при загрузке пользователей';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async create(data) {
            this.loading = true;
            this.error = null;

            try {
                const response = await userService.create(data);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Ошибка при создании пользователя';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async update(id, data) {
            this.loading = true;
            this.error = null;

            try {
                const response = await userService.update(id, data);

                const toast = useToast();
                toast.add({severity: 'success', summary: 'Успех', detail: 'Пользователь успешно обновлен', life: 3000});
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || `Ошибка при обновлении пользователя`;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async delete(id) {
            this.loading = true;
            this.error = null;

            try {
                return await userService.delete(id);
            } catch (error) {
                this.error = error.response?.data?.message || `Ошибка при удалении пользователя`;
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async show(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await userService.show(id);
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || `Ошибка при загрузке пользователя`;
                throw error;
            } finally {
                this.loading = false;
            }
        },
    }
});
