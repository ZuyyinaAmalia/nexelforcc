import { createRouter, createWebHistory } from 'vue-router'

// 1. Import Halaman PUBLIC (User Biasa/Penjual)
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'

// 2. Import Halaman ADMIN (Dari folder src/views/admin/)
import LoginAdmin from '../views/admin/LoginAdmin.vue'
import AdminLayout from '../views/admin/AdminLayout.vue'
import Dashboard from '../views/admin/Dashboard.vue'
import VerifikasiPenjual from '../views/admin/VerifikasiPenjual.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // --- ROUTE PUBLIC ---
    {
      path: '/',
      redirect: '/login'
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

    // --- ROUTE ADMIN ---
    {
      path: '/admin/login', // Login khusus admin
      name: 'admin-login',
      component: LoginAdmin
    },
    {
      path: '/admin',
      component: AdminLayout, // Layout Sidebar
      meta: { requiresAuth: true, role: 'admin' }, // Wajib Login & Wajib Admin
      children: [
        {
          path: '',
          redirect: '/admin/dashboard' // Default buka dashboard
        },
        {
          path: 'dashboard', // URL: /admin/dashboard
          name: 'admin-dashboard',
          component: Dashboard
        },
        {
          path: 'verifikasi-penjual', // URL: /admin/verifikasi-penjual
          name: 'admin-verifikasi',
          component: VerifikasiPenjual
        }
      ]
    }
  ]
})

// --- NAVIGATION GUARD (SATPAM) ---
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role') // 'admin' atau 'penjual'

  // Cek apakah halaman butuh login?
  if (to.meta.requiresAuth) {
    // 1. Kalau gak punya token, tendang keluar
    if (!token) {
      if (to.path.startsWith('/admin')) {
        return next('/admin/login') // Tendang ke login admin
      } else {
        return next('/login') // Tendang ke login biasa
      }
    }

    // 2. Kalau punya token, cek ROLE-nya (Cegah Penjual masuk Admin)
    if (to.meta.role && to.meta.role !== role) {
      alert('Akses Ditolak! Anda tidak memiliki izin.')
      return next('/login')
    }
  }

  // Khusus: Jika Admin sudah login tapi mau buka halaman login admin lagi
  if (to.path === '/admin/login' && token && role === 'admin') {
    return next('/admin/dashboard')
  }

  next()
})

export default router