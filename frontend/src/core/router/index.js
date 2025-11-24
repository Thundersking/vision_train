import {createRouter, createWebHistory} from 'vue-router'

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/login',
        component: () => import('@/domains/auth/views/Login.vue'),
        meta: {
            layout: 'auth',
            requiresAuth: false
        }
    },
    {
        path: '/forbidden',
        name: 'forbidden',
        component: () => import('@/domains/auth/views/Forbidden.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: '403 - Доступ запрещен'
        }
    },
    {
        path: '/dashboard',
        component: () => import('@/domains/dashboard/views/Overview.vue'),
        meta: {
            layout: 'dashboard',
            requiresAuth: true,
            title: 'Dashboard'
        }
    },
    {
        path: '/dashboard/analytics',
        component: () => import('@/domains/dashboard/views/Analytics.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Аналитика'
        }
    },
    {
        path: '/patients',
        component: () => import('@/domains/patients/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Пациенты'
        }
    },
    {
        path: '/users',
        name: 'users',
        component: () => import('@/domains/users/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Пользователи'
        }
    },
    {
        path: '/users/create',
        name: 'user-create',
        component: () => import('@/domains/users/views/Create.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Создание пользователя'
        }
    },
    {
        path: '/users/:uuid',
        name: 'user-show',
        component: () => import('@/domains/users/views/Show.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Просмотр пользователя'
        }
    },
    {
        path: '/users/:uuid/update',
        name: 'user-update',
        component: () => import('@/domains/users/views/Update.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Редактирование пользователя'
        }
    },
    {
        path: '/reports',
        component: () => import('@/domains/reports/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Отчеты'
        }
    },
    {
        path: '/reports/medical',
        component: () => import('@/domains/reports/views/Medical.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Медицинские отчеты'
        }
    },
    {
        path: '/reports/statistics',
        component: () => import('@/domains/reports/views/Statistics.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Статистические отчеты'
        }
    },
    {
        path: '/profile',
        component: () => import('@/domains/auth/views/Profile.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Профиль'
        }
    },
    {
        path: '/settings',
        component: () => import('@/domains/auth/views/Settings.vue'),
        meta: {
            layout: 'dashboard',
            // requiresAuth: true,
            title: 'Настройки'
        }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: () => import('@/common/views/NotFound.vue'),
        meta: {
            layout: 'dashboard',
            requiresAuth: false,
            title: '404 - Страница не найдена'
        }
    }
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
})

// Navigation guard для проверки авторизации
router.beforeEach((to, from, next) => {
    // Проверяем, требует ли маршрут авторизации
    if (to.meta.requiresAuth) {
        // Здесь должна быть логика проверки токена
        const token = localStorage.getItem('authToken')
        if (!token) {
            next('/login')
            return
        }
    }

    // Если пользователь авторизован и пытается попасть на страницу логина
    if (to.path === '/login') {
        const token = localStorage.getItem('authToken')
        if (token) {
            next('/dashboard')
            return
        }
    }

    // Устанавливаем заголовок страницы
    if (to.meta.title) {
        document.title = `${to.meta.title} - Vision Train`
    }

    next()
})

export default router
