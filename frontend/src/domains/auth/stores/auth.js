import { defineStore } from 'pinia'
import Cookies from 'js-cookie'
import router from '../../../core/router/index.js'
import {authService} from "@/domains/auth/services/auth.service.js";

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: safeJSONParse(localStorage.getItem('user'), null),
        token: localStorage.getItem('token') || null,
        isAuthenticated: !!localStorage.getItem('token'),
    }),

    getters: {
        // Информация о пользователе
        userName: (state) => state.user?.name || 'Пользователь',
        userEmail: (state) => state.user?.email,
        userInitials: (state) => state.user?.initials || 'У',
        userRoles: (state) => state.user?.roles || [],
        userPermissions: (state) => state.user?.permissions || [],
        
        // Проверки разрешений
        hasPermission: (state) => (permission) => {
            return state.user?.permissions?.includes(permission) || false
        },
        
        hasRole: (state) => (role) => {
            return state.user?.roles?.includes(role) || false
        },
        
        // Организационная информация
        organizationId: (state) => state.user?.organization_id,
        departmentId: (state) => state.user?.department_id,
        timezone: (state) => state.user?.timezone_display || 'UTC'
    },

    actions: {
        setToken(token) {
            this.token = token
            localStorage.setItem('token', token)
            this.isAuthenticated = !!token
        },

        setUser(user) {
            this.user = user
            localStorage.setItem('user', JSON.stringify(user))
        },

        async login(credentials) {
            try {
                const response = await authService.login(credentials)
                const { access_token, user, token_type, expires_in } = response.data

                // Сохраняем access token
                this.setToken(access_token)
                
                // Сохраняем пользователя
                this.setUser(user)

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