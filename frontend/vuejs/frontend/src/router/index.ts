import { createRouter, createWebHistory } from 'vue-router'
// Import halaman yang sudah kita buat
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/login' // Default buka langsung ke Login
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    // Jika nanti kamu punya dashboard, tambahkan di sini
    // {
    //   path: '/dashboard',
    //   name: 'dashboard',
    //   component: () => import('../views/DashboardView.vue')
    // }
  ]
})

export default router