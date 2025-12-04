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

import { 
  UsersIcon, 
  ChatBubbleLeftRightIcon, // atau StarIcon
  ChartBarIcon, 
  CubeIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/solid'

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
      
      <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-purple-500 flex justify-between items-center transition-transform hover:-translate-y-1">
        <div>
          <p class="text-gray-500 text-sm font-medium">Total Penjual</p>
          <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ stats?.user_penjual_status.total || 0 }}</h3>
          <p class="text-purple-600 text-xs mt-1 font-medium bg-purple-50 px-2 py-1 rounded-md inline-block">
            {{ stats?.user_penjual_status.aktif || 0 }} Aktif
          </p>
        </div>
        <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center shrink-0">
          <UsersIcon class="w-6 h-6 text-purple-600" />
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-pink-500 flex justify-between items-center transition-transform hover:-translate-y-1">
        <div>
          <p class="text-gray-500 text-sm font-medium">Total Review</p>
          <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ stats?.pengunjung_review_stats.total_reviews || 0 }}</h3>
          <p class="text-gray-400 text-xs mt-1">
            Dari {{ stats?.pengunjung_review_stats.unique_visitors || 0 }} pengunjung
          </p>
        </div>
        <div class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center shrink-0">
          <ChatBubbleLeftRightIcon class="w-6 h-6 text-pink-500" />
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500 flex justify-between items-center transition-transform hover:-translate-y-1">
        <div>
          <p class="text-gray-500 text-sm font-medium">Rata-rata Rating</p>
          <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ stats?.pengunjung_review_stats.average_rating || 0 }}</h3>
          <div class="flex items-center gap-1 mt-1">
            <span class="text-yellow-400 text-xs">★★★★★</span>
            <span class="text-gray-400 text-xs">(Skala 1-5)</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center shrink-0">
          <ChartBarIcon class="w-6 h-6 text-green-600" />
        </div>
      </div>

      <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500 flex justify-between items-center transition-transform hover:-translate-y-1">
        <div class="flex-1 pr-4"> <div class="flex justify-between items-center w-full mb-1">
                <p class="text-gray-500 text-sm font-medium">Total Produk</p>
            </div>
          
          <h3 class="text-3xl font-bold text-gray-800 mt-1">{{ totalProduk }}</h3>
          
          <div class="flex items-center justify-between mt-2">
            <p class="text-gray-400 text-xs">Semua kategori</p>
             <button 
                @click="downloadReport('produk-rating')"
                :disabled="downloading"
                class="text-[10px] bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 px-2 py-1 rounded transition-colors flex items-center gap-1"
                title="Unduh Laporan"
              >
                <span v-if="downloading">⏳</span>
                <span v-else>⬇️ PDF</span>
            </button>
          </div>
        </div>
        
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0">
          <CubeIcon class="w-6 h-6 text-blue-600" />
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Produk per Kategori</h3>
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100">
              <th class="text-left py-2 text-gray-600 text-sm">Kategori</th>
              <th class="text-right py-2 text-gray-600 text-sm">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in stats?.produk_per_kategori" :key="item.kategori" class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
              <td class="py-3 text-sm text-gray-700">{{ item.kategori }}</td>
              <td class="text-right font-bold text-gray-800 text-sm">{{ item.jumlah }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-bold text-gray-800">Status Penjual</h3>
          
          <button 
            @click="downloadReport('status')" 
            :disabled="downloading"
            class="text-xs font-medium bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors flex items-center gap-1"
          >
            <span v-if="downloading">⏳...</span>
            <span v-else>⬇️ PDF</span>
          </button>
        </div>

        <div class="space-y-4">
          <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg border border-green-100">
            <span class="font-medium text-gray-700">Aktif</span>
            <span class="text-2xl font-bold text-green-600">{{ stats?.user_penjual_status.aktif }}</span>
          </div>
          <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg border border-red-100">
            <span class="font-medium text-gray-700">Tidak Aktif</span>
            <span class="text-2xl font-bold text-red-600">{{ stats?.user_penjual_status.tidak_aktif }}</span>
          </div>
        </div>
      </div>
    </div>

     <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Toko per Provinsi</h3>
        
        <button 
          @click="downloadReport('provinsi')" 
          :disabled="downloading"
          class="text-xs font-medium bg-purple-50 text-purple-700 px-3 py-1.5 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors flex items-center gap-1"
        >
          <span v-if="downloading">⏳...</span>
          <span v-else>⬇️ PDF</span>
        </button>
      </div>

      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="text-left py-2 text-gray-600 text-sm">Provinsi</th>
            <th class="text-right py-2 text-gray-600 text-sm">Jumlah Toko</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats?.penjual_per_provinsi" :key="item.provinsi" class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
            <td class="py-3 text-sm text-gray-700">{{ item.provinsi }}</td>
            <td class="text-right font-bold text-gray-800 text-sm">{{ item.jumlah }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <h3 class="text-lg font-bold text-gray-800 mb-4">Distribusi Rating</h3>
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-100">
            <th class="text-left py-2 text-gray-600 text-sm">Rating</th>
            <th class="text-right py-2 text-gray-600 text-sm">Jumlah Review</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in stats?.pengunjung_review_stats.rating_distribution" :key="item.rating" class="border-b border-gray-50 last:border-0 hover:bg-gray-50">
            <td class="py-3 text-sm text-gray-700 flex items-center gap-1">
                {{ item.rating }} <span class="text-yellow-400">★</span>
            </td>
            <td class="text-right font-bold text-gray-800 text-sm">{{ item.jumlah }}</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>