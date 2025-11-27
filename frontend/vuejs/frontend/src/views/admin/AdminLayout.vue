<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const handleLogout = () => {
  const confirmLogout = confirm("Yakin ingin keluar?")
  if (!confirmLogout) return

  // Hapus data di local storage
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  
  // Kembali ke login admin
  router.push('/admin/login')
}
</script>

<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    
    <aside class="w-64 bg-white shadow-xl flex flex-col z-10">
      <div class="h-16 flex items-center justify-center border-b bg-blue-600">
        <h1 class="text-xl font-bold text-white tracking-wider">ADMIN PANEL</h1>
      </div>
      
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase mb-2 px-4">Menu Utama</p>
        
        <router-link 
          to="/admin/dashboard" 
          class="flex items-center px-4 py-3 text-gray-600 transition-colors duration-200 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          active-class="bg-blue-100 text-blue-700 font-medium"
        >
          <span class="mr-3">📊</span> Dashboard
        </router-link>

        <router-link 
          to="/admin/verifikasi-penjual" 
          class="flex items-center px-4 py-3 text-gray-600 transition-colors duration-200 rounded-lg hover:bg-blue-50 hover:text-blue-600"
          active-class="bg-blue-100 text-blue-700 font-medium"
        >
          <span class="mr-3">✅</span> Verifikasi Penjual
        </router-link>
      </nav>

      <div class="p-4 border-t bg-gray-50">
        <button 
          @click="handleLogout" 
          class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400"
        >
          🚪 Logout
        </button>
      </div>
    </aside>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
      <header class="bg-white shadow h-16 flex items-center justify-between px-6">
        <h2 class="text-lg font-semibold text-gray-700">Selamat Datang, Admin</h2>
      </header>

      <div class="p-6">
        <router-view></router-view>
      </div>
    </main>

  </div>
</template>