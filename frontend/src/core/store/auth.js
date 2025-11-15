import { defineStore } from 'pinia'
import Cookies from 'js-cookie'
import router from '../router'
import {authService} from "@/core/services/auth.service.js";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: safeJSONParse(localStorage.getItem('user'), null),
        token: localStorage.getItem('token') || null,
        isAuthenticated: !!localStorage.getItem('token'),
    }),

    actions: {
        setToken(token) {
            this.token = token
            localStorage.setItem('token', token)
            this.isAuthenticated = !!token
        },

        async login(credentials) {
            try {
                const response = await authService.login(credentials)
                const { access_token, user } = response.data

                // Сохраняем токены
                this.setToken(access_token)

                this.isAuthenticated = true

                return response
            } catch (error) {
                console.error('Login error:', error)
                throw error
            }
        },

        async logout() {
            try {
                await authService.logout()
            } finally {
                this.clearAuth()
                await router.push({name: 'login'})
            }
        },

        clearAuth() {
            this.user = null
            this.token = null
            this.isAuthenticated = false
            localStorage.removeItem('token')
            localStorage.removeItem('user')
            Cookies.remove('refresh_token')
        }
    }
})

/**
 * Безопасный парсинг JSON
 * @param jsonString
 * @param defaultValue
 * @returns {*}
 */
const safeJSONParse = (jsonString, defaultValue) => {
    if (!jsonString) return defaultValue;
    try {
        return JSON.parse(jsonString);
    } catch (error) {
        console.error('Error parsing JSON:', error);
        return defaultValue;
    }
};