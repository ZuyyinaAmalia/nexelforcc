<script setup lang="ts">
import { useRouter, useRoute } from 'vue-router' // Import useRoute untuk deteksi menu aktif

const router = useRouter()
const route = useRoute() // Pakai ini untuk styling menu aktif

const handleLogout = () => {
  if (confirm("Yakin ingin keluar?")) {
    localStorage.removeItem('token')
    localStorage.removeItem('role')
    router.push('/admin/login')
  }
}

// Helper untuk class menu aktif
const isActive = (path: string) => route.path === path
</script>

<template>
  <div class="flex h-screen bg-[#F5F5F9] font-sans">
    
    <aside class="w-64 bg-white shadow-lg flex flex-col z-20">
      <div class="h-20 flex items-center px-8 border-b border-gray-100">
        <h1 class="text-2xl font-extrabold text-purple-600 tracking-wide">NEXEL</h1>
        <span class="ml-2 text-xs font-semibold text-gray-400 mt-1">Admin</span>
      </div>
      
      <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <p class="text-xs font-bold text-gray-400 uppercase mb-4 px-2 tracking-wider">Menu Utama</p>
        
        <router-link to="/admin/dashboard" 
          class="flex items-center px-4 py-3 transition-all duration-200 rounded-xl group"
          :class="isActive('/admin/dashboard') ? 'bg-purple-600 text-white shadow-md shadow-purple-200' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-600'"
        >
          <span class="mr-3 text-lg">📊</span> 
          <span class="font-medium">Dashboard</span>
        </router-link>

        <router-link to="/admin/verifikasi-penjual" 
          class="flex items-center px-4 py-3 transition-all duration-200 rounded-xl group"
          :class="isActive('/admin/verifikasi-penjual') ? 'bg-purple-600 text-white shadow-md shadow-purple-200' : 'text-gray-500 hover:bg-purple-50 hover:text-purple-600'"
        >
          <span class="mr-3 text-lg">✅</span> 
          <span class="font-medium">Verifikasi Penjual</span>
        </router-link>
      </nav>

      <div class="p-6 border-t border-gray-100">
        <button @click="handleLogout" class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
          <span class="mr-2">🚪</span> Logout
        </button>
      </div>
    </aside>

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F5F5F9]">
      <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-10">
        <h2 class="text-lg font-bold text-gray-800">
            {{ route.path === '/admin/dashboard' ? 'Dashboard Overview' : 'Kelola Penjual' }}
        </h2>
        <div class="flex items-center gap-3">
             <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold">A</div>
             <span class="text-sm font-medium text-gray-600">Administrator</span>
        </div>
      </header>

      <div class="p-8">
        <router-view></router-view>
      </div>
    </main>

  </div>
</template>