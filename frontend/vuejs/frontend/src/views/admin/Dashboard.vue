<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

interface Stats {
  produk_per_kategori: Array<{ kategori: string; jumlah: number }>
  penjual_per_provinsi: Array<{ provinsi: string; jumlah: number }>
  user_penjual_status: {
    aktif: number
    tidak_aktif: number
    total: number
  }
  pengunjung_review_stats: {
    total_reviews: number
    unique_visitors: number
    average_rating: number
    rating_distribution: Array<{ rating: number; jumlah: number }>
  }
}

const stats = ref<Stats | null>(null)
const loading = ref(true)

// --- TAMBAHAN: State untuk loading download ---
const downloading = ref(false)

const fetchDashboardStats = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/admin/dashboard/stats', {
      headers: { Authorization: `Bearer ${token}` }
    })
    stats.value = response.data
    console.log('Dashboard data:', response.data) // Debug
  } catch (error: any) {
    console.error('Error fetching dashboard stats:', error)
    console.error('Error response:', error.response?.data) // Debug detail error
    alert('Gagal memuat data dashboard: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

// --- TAMBAHAN: Fungsi Download PDF ---
// Update tipe parameter agar menerima 'produk-rating'
const downloadReport = async (type: 'status' | 'provinsi' | 'produk-rating') => {
  if (downloading.value) return
  downloading.value = true

  try {
    const token = localStorage.getItem('token')
    
    // Tentukan URL berdasarkan tipe
    let url = ''
    let filename = ''

    if (type === 'status') {
        url = 'http://127.0.0.1:8000/api/admin/reports/status-penjual'
        filename = 'Laporan_Status_Penjual.pdf'
    } else if (type === 'provinsi') {
        url = 'http://127.0.0.1:8000/api/admin/reports/penjual-provinsi'
        filename = 'Laporan_Penjual_Provinsi.pdf'
    } else if (type === 'produk-rating') {
        // INI TAMBAHAN BARU
        url = 'http://127.0.0.1:8000/api/admin/reports/produk-rating'
        filename = 'Laporan_Produk_Rating.pdf'
    }

    const response = await axios.get(url, {
      headers: { Authorization: `Bearer ${token}` },
      responseType: 'blob'
    })

    const downloadUrl = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = downloadUrl
    link.setAttribute('download', filename)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

  } catch (error) {
    console.error('Download error:', error)
    alert('Gagal mengunduh file PDF')
  } finally {
    downloading.value = false
  }
}

const totalProduk = computed(() => 
  stats.value?.produk_per_kategori.reduce((sum, item) => sum + item.jumlah, 0) || 0
)

onMounted(() => {
  fetchDashboardStats()
})
</script>

<template>
  <div v-if="loading" class="flex items-center justify-center h-64">
    <div class="text-gray-500 text-lg">Loading dashboard...</div>
  </div>

  <div v-else class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm font-medium">Total Penjual</p>
            <h3 class="text-3xl font-bold mt-2">{{ stats?.user_penjual_status.total || 0 }}</h3>
            <p class="text-purple-100 text-xs mt-2">{{ stats?.user_penjual_status.aktif || 0 }} Aktif</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-3xl">👥</span>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-pink-100 text-sm font-medium">Total Review</p>
            <h3 class="text-3xl font-bold mt-2">{{ stats?.pengunjung_review_stats.total_reviews || 0 }}</h3>
            <p class="text-pink-100 text-xs mt-2">
              Dari {{ stats?.pengunjung_review_stats.unique_visitors || 0 }} pengunjung
            </p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-3xl">⭐</span>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm font-medium">Rata-rata Rating</p>
            <h3 class="text-3xl font-bold mt-2">{{ stats?.pengunjung_review_stats.average_rating || 0 }}</h3>
            <p class="text-green-100 text-xs mt-2">Dari skala 1-5</p>
          </div>
          <div class="bg-white/20 p-3 rounded-lg">
            <span class="text-3xl">📊</span>
          </div>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg relative overflow-hidden group">
        
        <div class="flex flex-col justify-between h-full z-10 relative">
          
          <div class="w-full">
            <div class="flex justify-between items-start mb-2">
                <p class="text-blue-100 text-sm font-medium">Total Produk</p>
                
                <button 
                  @click="downloadReport('produk-rating')"
                  :disabled="downloading"
                  class="text-[10px] bg-white/20 hover:bg-white/30 text-white px-2 py-1 rounded backdrop-blur-sm transition-all flex items-center gap-1 shadow-sm border border-white/10"
                  title="Unduh Laporan Produk & Rating"
                >
                  <span v-if="downloading">⏳</span>
                  <span v-else>⬇️ PDF</span>
                </button>
            </div>

            <h3 class="text-3xl font-bold mt-1">{{ totalProduk }}</h3>
            <p class="text-blue-100 text-xs mt-1">Semua kategori</p>
          </div>
          
        </div>

        <div class="absolute bottom-4 right-4 bg-white/20 p-3 rounded-lg z-0">
            <span class="text-3xl">📦</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Produk per Kategori</h3>
        <table class="w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left py-2">Kategori</th>
              <th class="text-right py-2">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in stats?.produk_per_kategori" :key="item.kategori" class="border-b">
              <td class="py-2">{{ item.kategori }}</td>
              <td class="text-right font-bold">{{ item.jumlah }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-800">Status Penjual</h3>
          
          <button 
            @click="downloadReport('status')" 
            :disabled="downloading"
            class="text-xs font-medium bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors flex items-center gap-1"
          >
            <span v-if="downloading">⏳ Loading...</span>
            <span v-else>⬇️ Unduh PDF</span>
          </button>
        </div>

        <div class="space-y-4">
          <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
            <span class="font-medium text-gray-700">Aktif</span>
            <span class="text-2xl font-bold text-green-600">{{ stats?.user_penjual_status.aktif }}</span>
          </div>
          <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg">
            <span class="font-medium text-gray-700">Tidak Aktif</span>
            <span class="text-2xl font-bold text-red-600">{{ stats?.user_penjual_status.tidak_aktif }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Toko per Provinsi</h3>
        
        <button 
          @click="downloadReport('provinsi')" 
          :disabled="downloading"
          class="text-xs font-medium bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors flex items-center gap-1"
        >
          <span v-if="downloading">⏳ Loading...</span>
          <span v-else>⬇️ Unduh PDF</span>
        </button>
      </div>

      <table class="w-full">
        <thead>
          <tr class="border-b">
            <th class="text-left py-2">Provinsi</th>
            <th class="text-right py-2">Jumlah Toko</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats?.penjual_per_provinsi" :key="item.provinsi" class="border-b">
            <td class="py-2">{{ item.provinsi }}</td>
            <td class="text-right font-bold">{{ item.jumlah }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">Distribusi Rating</h3>
      <table class="w-full">
        <thead>
          <tr class="border-b">
            <th class="text-left py-2">Rating</th>
            <th class="text-right py-2">Jumlah Review</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats?.pengunjung_review_stats.rating_distribution" :key="item.rating" class="border-b">
            <td class="py-2">{{ item.rating }} ⭐</td>
            <td class="text-right font-bold">{{ item.jumlah }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>