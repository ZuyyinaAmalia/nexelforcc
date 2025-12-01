<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

// Definisi variabel (State)
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

// Set base URL default (bisa dipindah ke main.ts sebenarnya)
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    // Tembak API Login Admin
    const response = await axios.post('/admin/login', {
      email: email.value,
      password: password.value
    })

    // Simpan Token & Role
    localStorage.setItem('token', response.data.token) // Pastikan respon API key-nya 'token' atau 'access_token'
    localStorage.setItem('role', 'admin') 

    // Redirect ke Dashboard
    router.push('/admin/dashboard')

  } catch (error: any) {
    // Tangkap pesan error dari backend
    errorMessage.value = error.response?.data?.message || 'Login Gagal, cek email/password.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-[#F5F5F9]">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
      
      <div class="text-center mb-8">
          <h1 class="text-3xl font-extrabold text-purple-600 tracking-wide">NEXEL</h1>
          <p class="text-gray-400 text-sm mt-2">Portal Admin Marketplace</p>
      </div>
      
      <div v-if="errorMessage" class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm flex items-center border border-red-100">
        ⚠️ {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-5">
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Email Admin</label>
          <input 
            v-model="email" 
            type="email" 
            class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:bg-white focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition outline-none" 
            placeholder="admin@nexel.com" 
            required
          >
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-600 mb-1">Password</label>
          <input 
            v-model="password" 
            type="password" 
            class="w-full px-4 py-3 rounded-lg bg-gray-50 border-transparent focus:bg-white focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition outline-none" 
            placeholder="••••••••" 
            required
          >
        </div>
        <button 
          type="submit" 
          :disabled="isLoading" 
          class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-xl transition-all transform active:scale-95 shadow-lg shadow-purple-200 disabled:opacity-70 disabled:cursor-not-allowed"
        >
          {{ isLoading ? 'Memproses...' : 'Masuk Dashboard' }}
        </button>
      </form>
    </div>
  </div>
</template>