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
        name: 'patients',
        component: () => import('@/domains/patients/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Пациенты'
        }
    },
    {
        path: '/patients/create',
        name: 'patient-create',
        component: () => import('@/domains/patients/views/Create.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Создание пациента'
        }
    },
    {
        path: '/patients/:uuid',
        name: 'patient-show',
        component: () => import('@/domains/patients/views/Show.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Карточка пациента'
        }
    },
    {
        path: '/patients/:uuid/update',
        name: 'patient-update',
        component: () => import('@/domains/patients/views/Update.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Редактирование пациента'
        }
    },

    // Users
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

    // Departments
    {
        path: '/departments',
        name: 'departments',
        component: () => import('@/domains/departments/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Офисы'
        }
    },
    {
        path: '/departments/create',
        name: 'department-create',
        component: () => import('@/domains/departments/views/Create.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Создание офиса'
        }
    },
    {
        path: '/departments/:uuid',
        name: 'department-show',
        component: () => import('@/domains/departments/views/Show.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Просмотр офиса'
        }
    },
    {
        path: '/departments/:uuid/update',
        name: 'department-update',
        component: () => import('@/domains/departments/views/Update.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Редактирование офиса'
        }
    },

    // Organization
    {
        path: '/organization',
        name: 'organization-show',
        component: () => import('@/domains/organization/views/Show.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Моя организация'
        }
    },
    {
        path: '/organization/edit',
        name: 'organization-edit',
        component: () => import('@/domains/organization/views/Update.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Редактирование организации'
        }
    },

    // Exercise Templates
    {
        path: '/exercise-templates',
        name: 'exercise-templates',
        component: () => import('@/domains/exercise-templates/views/Index.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Шаблоны упражнений'
        }
    },
    {
        path: '/exercise-templates/create',
        name: 'exercise-template-create',
        component: () => import('@/domains/exercise-templates/views/Create.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Создание шаблона'
        }
    },
    {
        path: '/exercise-templates/:uuid/edit',
        name: 'exercise-template-update',
        component: () => import('@/domains/exercise-templates/views/Update.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Редактирование шаблона'
        }
    },
    {
        path: '/exercise-templates/:uuid',
        name: 'exercise-template-show',
        component: () => import('@/domains/exercise-templates/views/Show.vue'),
        meta: {
            layout: 'dashboard',
            title: 'Просмотр шаблона'
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
