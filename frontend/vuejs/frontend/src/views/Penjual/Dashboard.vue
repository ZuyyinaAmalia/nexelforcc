<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const activeMenu = ref('home');
const namaPenjual = ref('Penjual');

onMounted(() => {
  // Set active menu based on current route
  if (route.path.includes('kelolaproduk')) {
    activeMenu.value = 'produk';
  } else {
    activeMenu.value = 'home';
  }
  
  // Get seller name from localStorage
  const storedName = localStorage.getItem('nama_penjual');
  if (storedName) {
    namaPenjual.value = storedName;
  }
});

const navigateTo = (menu, path) => {
  activeMenu.value = menu;
  router.push(path);
};

const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('role');
  localStorage.removeItem('nama_penjual');
  router.push('/login');
};
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg fixed h-full">
      <div class="p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-purple-600">NEXEL</h1>
        <p class="text-sm text-gray-500 mt-1">Dashboard Penjual</p>
      </div>
      
      <nav class="p-4">
        <ul class="space-y-2">
          <li>
            <button
              @click="navigateTo('home', '/dashboard-penjual')"
              :class="[
                'w-full flex items-center px-4 py-3 rounded-lg text-left transition-all duration-200',
                activeMenu === 'home' 
                  ? 'bg-purple-600 text-white shadow-md' 
                  : 'text-gray-700 hover:bg-purple-50 hover:text-purple-600'
              ]"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <span class="font-medium">Home</span>
            </button>
          </li>
          
          <li>
            <button
              @click="navigateTo('produk', '/dashboard-penjual/kelolaproduk')"
              :class="[
                'w-full flex items-center px-4 py-3 rounded-lg text-left transition-all duration-200',
                activeMenu === 'produk' 
                  ? 'bg-purple-600 text-white shadow-md' 
                  : 'text-gray-700 hover:bg-purple-50 hover:text-purple-600'
              ]"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <span class="font-medium">Produk</span>
            </button>
          </li>
        </ul>
      </nav>
      
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200">
        <button
          @click="handleLogout"
          class="w-full flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-200"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span class="font-medium">Logout</span>
        </button>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" v-if="Component" />
          <div v-else>
            <!-- Home Content -->
            <div class="mb-8">
              <h2 class="text-3xl font-bold text-gray-900">Selamat Datang, {{ namaPenjual }}! 👋</h2>
              <p class="text-gray-600 mt-2">Selamat datang di dashboard penjual NEXEL</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-600">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 font-medium">Total Produk</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                  </div>
                  <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                </div>
              </div>
              
              <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-600">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 font-medium">Total Penjualan</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">Rp 0</p>
                  </div>
                  <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                </div>
              </div>
              
              <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-600">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 font-medium">Ulasan Baru</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">0</p>
                  </div>
                  <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6">
              <h3 class="text-xl font-bold text-gray-900 mb-4">Aktivitas Terbaru</h3>
              <div class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Belum ada aktivitas</p>
              </div>
            </div>
          </div>
        </transition>
      </router-view>
    </main>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>