import apiClient from './client'
import {useAuthStore} from '../../domains/auth/stores/auth.js'
import router from '../router'
import {useToast} from 'primevue/usetoast';

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
            const toast = useToast()

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
                    console.error('Ошибка при обновлении токена:', refreshError)
                    // Если не удалось обновить токен, выходим из аккаунта
                    await authStore.logout()
                    await router.push({name: 'login'})
                    toast.error('Сессия истекла. Пожалуйста, войдите снова.')
                    return Promise.reject(refreshError)
                }
            }

            if (error.response?.status === 422) {
                toast.error('Ошибка валидации')
                return Promise.reject(error);
            }

            // Обработка других ошибок API
            if (error.response) {
                let errorMessage = 'Произошла ошибка при обработке запроса'

                if (error.response.data && error.response.data.message) {
                    errorMessage = error.response.data.message
                } else if (error.response.data && error.response.data.errors) {
                    const errors = error.response.data.errors
                    errorMessage = Object.values(errors)[0][0] || errorMessage
                }

                toast.error(errorMessage)
            } else if (error.request) {
                toast.error('Сервер не отвечает. Проверьте подключение к интернету.')
            } else {
                toast.error('Ошибка при отправке запроса.')
            }

            return Promise.reject(error)
        }
    )
}

setupInterceptors()