import {createRouter, createWebHistory} from 'vue-router'

const routes = [
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
