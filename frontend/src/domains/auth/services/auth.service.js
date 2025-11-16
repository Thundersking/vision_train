import apiClient from "@/core/api/client.js";

export const authService = {
    /**
     * Авторизация пользователя
     * @param {Object} credentials - данные авторизации
     * @returns {Promise} Promise с ответом сервера
     */
    login(credentials) {
        return apiClient.post('/auth/login', credentials)
    },

    /**
     * Выход из системы
     * @returns {Promise} Promise с ответом сервера
     */
    logout() {
        return apiClient.post('/auth/logout')
    },

    /**
     * Обновление токена с помощью refresh token
     * @param {Object} data - refresh token
     * @returns {Promise} Promise с ответом сервера
     */
    refreshToken(data) {
        return apiClient.post('/auth/refresh', data)
    },
}