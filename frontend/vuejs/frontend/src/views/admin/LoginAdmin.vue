<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

// Set base URL default
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
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('role', 'admin') // Penanda bahwa ini admin

    // Redirect ke Dashboard
    router.push('/admin/dashboard')

  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Login Gagal'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-800">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin Portal</h2>
      
      <div v-if="errorMessage" class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email Admin</label>
          <input v-model="email" type="email" class="mt-1 block w-full border p-2 rounded" placeholder="admin@martplace.com" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="password" type="password" class="mt-1 block w-full border p-2 rounded" placeholder="••••••••" required>
        </div>
        <button type="submit" :disabled="isLoading" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:bg-blue-300">
          {{ isLoading ? 'Memproses...' : 'Masuk Sistem' }}
        </button>
      </form>
    </div>
  </div>
</template>