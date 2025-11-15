import {createRouter, createWebHistory} from 'vue-router'

const routes = [
    {
        path: '/login',
        component: () => import('@/domains/auth/views/Login.vue'),
    },
    {
        path: '/users',
        component: () => import('@/domains/users/views/Index.vue'),
    },
]

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
})

export default router
