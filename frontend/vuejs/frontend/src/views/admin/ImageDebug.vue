<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const penjualsData = ref<any[]>([])
const isLoading = ref(false)

const fetchData = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/admin/verifikasi-penjual')
    penjualsData.value = response.data
    console.log('Data fetched:', penjualsData.value)
  } catch (error) {
    console.error('Error:', error)
    alert('Gagal fetch data')
  } finally {
    isLoading.value = false
  }
}

const testImageUrl = async (url: string) => {
  try {
    const response = await fetch(url)
    return {
      status: response.status,
      ok: response.ok,
      contentType: response.headers.get('content-type')
    }
  } catch (error: any) {
    return {
      error: error.message
    }
  }
}

onMounted(() => {
  fetchData()
})
</script>

<template>
  <div class="p-8 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Image Debug Page</h1>
    
    <button 
      @click="fetchData"
      class="mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      :disabled="isLoading"
    >
      {{ isLoading ? 'Loading...' : 'Fetch Data' }}
    </button>

    <div class="space-y-8">
      <div v-for="p in penjualsData" :key="p.id" class="bg-white p-6 rounded-lg shadow border border-gray-200">
        <h2 class="text-xl font-bold mb-4">{{ p.namaToko }} (ID: {{ p.id }})</h2>
        
        <div class="grid grid-cols-2 gap-6">
          <!-- Foto URL Info -->
          <div class="bg-gray-50 p-4 rounded border border-gray-200">
            <h3 class="font-bold text-purple-600 mb-2">Foto Profil</h3>
            <div class="text-sm space-y-2">
              <p><strong>Raw foto field:</strong></p>
              <p class="text-xs bg-white p-2 rounded border text-gray-700 break-all">{{ p.foto }}</p>
              <p><strong>foto_url field:</strong></p>
              <p class="text-xs bg-white p-2 rounded border text-gray-700 break-all">{{ p.foto_url }}</p>
            </div>
            <div v-if="p.foto_url" class="mt-4">
              <img 
                :src="p.foto_url"
                class="max-w-[200px] max-h-[200px] rounded border"
                @load="console.log('Foto loaded successfully')"
                @error="console.error('Foto failed to load:', p.foto_url)"
              >
            </div>
          </div>

          <!-- Foto KTP Info -->
          <div class="bg-gray-50 p-4 rounded border border-gray-200">
            <h3 class="font-bold text-purple-600 mb-2">Foto KTP</h3>
            <div class="text-sm space-y-2">
              <p><strong>Raw fotoKtp field:</strong></p>
              <p class="text-xs bg-white p-2 rounded border text-gray-700 break-all">{{ p.fotoKtp }}</p>
              <p><strong>foto_ktp_url field:</strong></p>
              <p class="text-xs bg-white p-2 rounded border text-gray-700 break-all">{{ p.foto_ktp_url }}</p>
            </div>
            <div v-if="p.foto_ktp_url" class="mt-4">
              <img 
                :src="p.foto_ktp_url"
                class="max-w-[200px] max-h-[200px] rounded border"
                @load="console.log('KTP loaded successfully')"
                @error="console.error('KTP failed to load:', p.foto_ktp_url)"
              >
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
