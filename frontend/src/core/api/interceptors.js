import apiClient from './client'
import router from '../router'
import {useAuthStore} from "@/domains/auth/stores/auth.js";

export const setupInterceptors = () => {
    /**
     * Добавление токена авторизации
     */
    apiClient.interceptors.request.use(
        config => {
            const authStore = useAuthStore()
            if (authStore.token) {
                config.headers.Authorization = `Bearer ${authStore.token}`
            }
            return config
        },
        error => Promise.reject(error)
    )

    /**
     * Обработка ошибок и рефреша токена
     */
    apiClient.interceptors.response.use(
        response => response,
        async error => {
            const originalRequest = error.config

            if (originalRequest.url.includes('/auth/refresh') || originalRequest._retry) {
                return Promise.reject(error)
            }

            const isAuthRequest =
                originalRequest.url.includes('/auth/login') ||
                originalRequest.url.includes('/auth/register') ||
                originalRequest.url.includes('/auth/logout');

            // для автор. запросов просто возвращаем ошибку
            if (isAuthRequest) {
                return Promise.reject(error);
            }

            const authStore = useAuthStore()

            if (error.response?.status === 401 && !originalRequest._retry) {
                originalRequest._retry = true

                try {
                    const response = await apiClient.post('/auth/refresh')

                    const {access_token} = response.data
                    authStore.setToken(access_token)

                    // Повторяем запрос с новым токеном
                    originalRequest.headers.Authorization = `Bearer ${access_token}`
                    return apiClient(originalRequest)
                } catch (refreshError) {
                    // Если не удалось обновить токен, выходим из аккаунта
                    await authStore.logout()
                    await router.push({name: 'login'})
                    return Promise.reject(refreshError)
                }
            }

            if (error.response?.status === 403) {
                await router.push({'name': 'forbidden'});
            }

            if (error.response?.status === 422) {
                return Promise.reject(error);
            }

            return Promise.reject(error)
        }
    )
}

setupInterceptors()