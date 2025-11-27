<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Definisi Tipe Data Penjual (Agar TS tidak error)
interface Penjual {
  id: number
  namaToko: string
  namaPenjual: string
  email: string
  noHp: string
  status: string
  created_at: string
}

const penjuals = ref<Penjual[]>([])
const isLoading = ref(false)

// Setting Header Authorization (Wajib untuk Sanctum)
const token = localStorage.getItem('token')
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'

// 1. Ambil Data Pending
const fetchPendingSellers = async () => {
  try {
    const response = await axios.get('/admin/verifikasi-penjual')
    penjuals.value = response.data
  } catch (error) {
    console.error("Gagal mengambil data:", error)
    alert("Gagal mengambil data penjual.")
  }
}

// 2. Proses Verifikasi
const verifikasi = async (id: number, status: 'ACTIVE' | 'REJECTED') => {
  const konfirmasi = confirm(`Apakah Anda yakin ingin mengubah status menjadi ${status}?`)
  if (!konfirmasi) return

  isLoading.value = true
  try {
    // Tembak API Laravel
    await axios.post(`/admin/verifikasi-penjual/${id}`, { status })
    
    alert(`Berhasil! Penjual telah di-${status}`)
    
    // Refresh tabel (data yang sudah diverifikasi akan hilang dari list)
    fetchPendingSellers()
    
  } catch (error) {
    console.error(error)
    alert("Terjadi kesalahan saat memproses verifikasi.")
  } finally {
    isLoading.value = false
  }
}

// Jalankan saat halaman dibuka
onMounted(() => {
  fetchPendingSellers()
})
</script>

<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Verifikasi Akun Penjual</h2>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
            <th class="py-3 px-6 text-left">Tgl Daftar</th>
            <th class="py-3 px-6 text-left">Nama Toko</th>
            <th class="py-3 px-6 text-left">Pemilik</th>
            <th class="py-3 px-6 text-left">Kontak</th>
            <th class="py-3 px-6 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
          <tr v-for="p in penjuals" :key="p.id" class="border-b border-gray-200 hover:bg-gray-50">
            <td class="py-3 px-6 text-left whitespace-nowrap">
              {{ new Date(p.created_at).toLocaleDateString('id-ID') }}
            </td>
            <td class="py-3 px-6 text-left font-medium">{{ p.namaToko }}</td>
            <td class="py-3 px-6 text-left">{{ p.namaPenjual }}</td>
            <td class="py-3 px-6 text-left">
              <div>{{ p.email }}</div>
              <div class="text-xs text-gray-500">{{ p.noHp }}</div>
            </td>
            <td class="py-3 px-6 text-center">
              <div class="flex item-center justify-center gap-2">
                <button 
                  @click="verifikasi(p.id, 'ACTIVE')" 
                  :disabled="isLoading"
                  class="bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded text-xs transition disabled:opacity-50"
                >
                  Terima
                </button>
                
                <button 
                  @click="verifikasi(p.id, 'REJECTED')" 
                  :disabled="isLoading"
                  class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-xs transition disabled:opacity-50"
                >
                  Tolak
                </button>
              </div>
            </td>
          </tr>
          
          <tr v-if="penjuals.length === 0">
            <td colspan="5" class="py-8 text-center text-gray-500">
              Tidak ada permintaan verifikasi penjual saat ini.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>