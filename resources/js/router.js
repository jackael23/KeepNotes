import { createRouter, createWebHistory } from 'vue-router'
const routes = [
    {
        path: '/home',
        name: 'Home',
        component: () => import('@pages/Home.vue')
    },
    {
        path: '/',
        name: 'Login',
        component: () => import('@pages/Login.vue')
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('@pages/Register.vue')
    },
    {
        path: '/reminder',
        name: 'Reminder',
        component: () => import('@pages/Reminder.vue')
    },
    {
        path: '/archive',
        name: 'Archive',
        component: () => import('@pages/Archive.vue')
    },
    {
        path: '/trash',
        name: 'Trash',
        component: () => import('@pages/Trash.vue')
    },
    
]
const router = createRouter({
    history: createWebHistory(),
    routes
})
export default router