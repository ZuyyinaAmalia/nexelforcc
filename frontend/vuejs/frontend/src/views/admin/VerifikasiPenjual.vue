<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

// Deklarasi window global untuk TypeScript
declare global {
  interface Window {
    open(url: string, target?: string): Window | null
  }
}

// Setup Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'
const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

interface Alamat {
    id?: number
    jalan: string
    rt?: string
    rw?: string
    desa: string
    kota: string
    provinsi: string
    kode_pos?: string
}

interface Penjual {
  id: number
  namaToko: string
  namaPenjual: string
  email: string
  noHp: string
  nik: string
  deskripsiToko: string
  foto: string | null 
  fotoKtp: string | null
  status: string
  created_at: string
  alamat?: Alamat 
}

const penjuals = ref<Penjual[]>([])
const isLoading = ref(false)
const showModal = ref(false)
const selectedPenjual = ref<Penjual | null>(null)
const showRejectInput = ref(false) 
const rejectReason = ref('') 

const storageUrl = 'http://127.0.0.1:8000/storage/'

const fetchPendingSellers = async () => {
  isLoading.value = true
  try {
    console.log('Fetching pending sellers...')
    console.log('URL:', axios.defaults.baseURL + '/admin/verifikasi-penjual')
    console.log('Token:', token ? 'Ada' : 'Tidak ada')
    
    const response = await axios.get('/admin/verifikasi-penjual')
    
    console.log('Response status:', response.status)
    console.log('Response data:', response.data)
    
    if (Array.isArray(response.data)) {
      penjuals.value = response.data
      console.log(`Berhasil load ${penjuals.value.length} penjual pending`)
      
      // Log setiap penjual untuk debugging
      penjuals.value.forEach((p, idx) => {
        console.log(`Penjual ${idx + 1}:`, {
          id: p.id,
          nama: p.namaPenjual,
          toko: p.namaToko,
          alamat: p.alamat ? 'Ada' : 'Tidak ada'
        })
      })
    } else {
      console.error('Data bukan array:', response.data)
      penjuals.value = []
    }
  } catch (error: any) {
    console.error('Error fetching sellers:', error)
    
    if (error.response) {
      console.error('Response error:', error.response.data)
      console.error('Status:', error.response.status)
      
      if (error.response.status === 401) {
        alert('Session habis. Silakan login kembali.')
        localStorage.clear()
        window.location.href = '/admin/login'
      } else {
        alert('Gagal memuat data: ' + (error.response.data.message || error.message))
      }
    } else if (error.request) {
      console.error('No response received:', error.request)
      alert('Tidak ada respon dari server. Pastikan backend berjalan.')
    } else {
      console.error('Error:', error.message)
      alert('Terjadi kesalahan: ' + error.message)
    }
  } finally {
    isLoading.value = false
  }
}

const openModal = (penjual: Penjual) => {
    console.log('Opening modal for:', penjual.namaPenjual)
    console.log('Alamat data:', penjual.alamat)
    selectedPenjual.value = penjual
    showModal.value = true
    showRejectInput.value = false 
    rejectReason.value = ''
}

const closeModal = () => {
    showModal.value = false
    selectedPenjual.value = null
    showRejectInput.value = false
    rejectReason.value = ''
}

const processVerifikasi = async (status: 'ACTIVE' | 'REJECTED') => {
    if (!selectedPenjual.value) return

    if (status === 'REJECTED' && !showRejectInput.value) {
        showRejectInput.value = true 
        return
    }

    if (status === 'REJECTED' && !rejectReason.value.trim()) {
        alert("Wajib mengisi alasan penolakan!")
        return
    }

    const confirmMsg = status === 'ACTIVE' 
        ? `Yakin terima akun "${selectedPenjual.value.namaToko}"?` 
        : `Yakin tolak akun "${selectedPenjual.value.namaToko}" dengan alasan tersebut?`

    if (!confirm(confirmMsg)) return

    isLoading.value = true
    try {
        console.log('Mengirim verifikasi:', {
          id: selectedPenjual.value.id,
          status,
          alasan: rejectReason.value
        })

        const response = await axios.post(`/admin/verifikasi-penjual/${selectedPenjual.value.id}`, {
            status: status,
            alasan: status === 'REJECTED' ? rejectReason.value : null
        })

        console.log('Response verifikasi:', response.data)
        
        const successMsg = status === 'ACTIVE' 
          ? `Berhasil! Akun "${selectedPenjual.value.namaToko}" telah diaktifkan.`
          : `Akun "${selectedPenjual.value.namaToko}" telah ditolak.`
        
        alert(successMsg)
        closeModal()
        await fetchPendingSellers() 

    } catch (error: any) {
        console.error('Error saat verifikasi:', error)
        
        if (error.response) {
          console.error('Error response:', error.response.data)
          alert("Gagal memproses: " + (error.response.data.message || error.message))
        } else {
          alert("Terjadi kesalahan: " + error.message)
        }
    } finally {
        isLoading.value = false
    }
}

const getImageUrl = (path: string | null) => {
    if (!path) return 'https://via.placeholder.com/150?text=No+Image'
    if (path.startsWith('http')) return path
    return `${storageUrl}${path}`
}

const formatDate = (dateString: string) => {
  try {
    return new Date(dateString).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return '-'
  }
}

const openImageInNewTab = (imageUrl: string) => {
  window.open(imageUrl, '_blank')
}

onMounted(() => {
  console.log('Component mounted')
  console.log('Token tersimpan:', token ? 'Ya' : 'Tidak')
  console.log('Role:', localStorage.getItem('role'))
  fetchPendingSellers()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Verifikasi Penjual</h2>
            <p class="text-gray-500 text-sm">Review data pendaftar sebelum mengaktifkan akun.</p>
        </div>
        <button 
          @click="fetchPendingSellers" 
          class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition disabled:bg-gray-400"
          :disabled="isLoading"
        >
          <span v-if="isLoading">Loading...</span>
          <span v-else>Refresh</span>
        </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading && penjuals.length === 0" class="bg-white rounded-xl shadow-sm border p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
      <p class="text-gray-500">Memuat data penjual...</p>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
      <table class="min-w-full leading-normal">
        <thead>
          <tr class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
            <th class="py-4 px-6 text-left">Tanggal</th>
            <th class="py-4 px-6 text-left">Info Toko</th>
            <th class="py-4 px-6 text-left">Pemilik</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
          <tr 
            v-for="p in penjuals" 
            :key="p.id" 
            class="border-b border-gray-100 hover:bg-purple-50 transition duration-150"
          >
            <td class="py-4 px-6 whitespace-nowrap">
               <span class="font-medium">{{ formatDate(p.created_at) }}</span>
            </td>
            <td class="py-4 px-6">
                <div class="flex items-center">
                    <img 
                      :src="getImageUrl(p.foto)" 
                      class="w-10 h-10 rounded-full object-cover border mr-3 bg-gray-100" 
                      alt="Foto"
                      @error="(e: any) => e.target.src = 'https://via.placeholder.com/150?text=No+Image'"
                    >
                    <div>
                        <div class="font-bold text-gray-800">{{ p.namaToko || '-' }}</div>
                        <div class="text-xs text-gray-500 truncate max-w-[200px]">
                          {{ p.deskripsiToko || 'Tidak ada deskripsi' }}
                        </div>
                    </div>
                </div>
            </td>
            <td class="py-4 px-6">
                <div class="font-medium">{{ p.namaPenjual || '-' }}</div>
                <div class="text-xs text-gray-500">{{ p.nik || '-' }}</div>
            </td>
            <td class="py-4 px-6 text-center">
                <span class="bg-yellow-100 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold uppercase">
                  {{ p.status || 'PENDING' }}
                </span>
            </td>
            <td class="py-4 px-6 text-center">
              <button 
                @click="openModal(p)" 
                class="text-purple-600 hover:text-purple-800 font-semibold text-sm hover:underline transition"
              >
                Lihat Detail
              </button>
            </td>
          </tr>
          
          <!-- Empty State -->
          <tr v-if="penjuals.length === 0 && !isLoading">
            <td colspan="5" class="py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <span class="text-4xl mb-2">📭</span>
                    <p class="font-medium">Tidak ada permintaan verifikasi saat ini.</p>
                    <p class="text-sm mt-1">Semua penjual sudah diverifikasi</p>
                </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Detail -->
    <div v-if="showModal && selectedPenjual" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

        <!-- Modal Content -->
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl z-10 overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="bg-purple-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Detail Verifikasi Penjual</h3>
                <button 
                  @click="closeModal" 
                  class="hover:bg-purple-700 rounded-full p-1 transition text-xl w-8 h-8 flex items-center justify-center"
                >
                  ✕
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kolom Kiri -->
                    <div class="space-y-4">
                        <!-- Data Pribadi -->
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <h4 class="text-purple-600 font-bold mb-3 uppercase text-xs tracking-wider">Data Pribadi</h4>
                            <div class="flex items-center gap-4 mb-4">
                                <img 
                                  :src="getImageUrl(selectedPenjual.foto)" 
                                  class="w-16 h-16 rounded-full object-cover border-2 border-purple-100 bg-gray-100"
                                  @error="(e: any) => e.target.src = 'https://via.placeholder.com/150?text=No+Image'"
                                >
                                <div>
                                    <p class="font-bold text-lg text-gray-800">{{ selectedPenjual.namaPenjual }}</p>
                                    <p class="text-gray-500 text-sm">NIK: {{ selectedPenjual.nik }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-semibold w-20 inline-block">Email:</span> {{ selectedPenjual.email }}</p>
                                <p><span class="font-semibold w-20 inline-block">No HP:</span> {{ selectedPenjual.noHp }}</p>
                            </div>
                        </div>

                        <!-- Data Alamat -->
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <h4 class="text-purple-600 font-bold mb-3 uppercase text-xs tracking-wider">Data Alamat</h4>
                            <div v-if="selectedPenjual.alamat" class="text-sm text-gray-700 space-y-1">
                                <p><strong>Jalan:</strong> {{ selectedPenjual.alamat.jalan }}</p>
                                <p v-if="selectedPenjual.alamat.rt || selectedPenjual.alamat.rw">
                                  <strong>RT/RW:</strong> {{ selectedPenjual.alamat.rt || '-' }} / {{ selectedPenjual.alamat.rw || '-' }}
                                </p>
                                <p><strong>Desa:</strong> {{ selectedPenjual.alamat.desa }}</p>
                                <p><strong>Kota:</strong> {{ selectedPenjual.alamat.kota }}</p>
                                <p><strong>Provinsi:</strong> {{ selectedPenjual.alamat.provinsi }}</p>
                            </div>
                            <p class="text-sm text-gray-400 italic" v-else>
                              ⚠️ Data alamat tidak ditemukan
                            </p>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="space-y-4">
                        <!-- Data Toko -->
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <h4 class="text-purple-600 font-bold mb-3 uppercase text-xs tracking-wider">Data Toko</h4>
                            <p class="font-bold text-lg">{{ selectedPenjual.namaToko }}</p>
                            <p class="text-gray-600 text-sm mt-1">{{ selectedPenjual.deskripsiToko }}</p>
                        </div>

                        <!-- Foto KTP -->
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                            <h4 class="text-purple-600 font-bold mb-3 uppercase text-xs tracking-wider">Foto KTP</h4>
                            <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden border">
                                <img 
                                  :src="getImageUrl(selectedPenjual.fotoKtp)" 
                                  class="w-full h-full object-contain cursor-pointer hover:scale-105 transition" 
                                  @click="openImageInNewTab(getImageUrl(selectedPenjual.fotoKtp))" 
                                  title="Klik untuk memperbesar"
                                  @error="(e: any) => e.target.src = 'https://via.placeholder.com/400x300?text=Foto+KTP+Tidak+Tersedia'"
                                >
                            </div>
                            <p class="text-xs text-gray-500 mt-2 text-center">
                              💡 Klik gambar untuk memperbesar
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Penolakan -->
                <div v-if="showRejectInput" class="mt-6 bg-red-50 p-4 rounded-xl border border-red-100 animate-fade-in">
                    <label class="block text-red-700 font-bold mb-2 text-sm">Alasan Penolakan (Wajib Diisi):</label>
                    <textarea 
                        v-model="rejectReason" 
                        class="w-full p-3 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:outline-none" 
                        rows="3" 
                        placeholder="Contoh: Foto KTP tidak jelas, data tidak lengkap, dll..."
                    ></textarea>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-white p-4 border-t flex justify-end gap-3">
                <button 
                  @click="closeModal" 
                  class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg font-medium transition"
                  :disabled="isLoading"
                >
                    Batal
                </button>
                
                <button 
                    v-if="!showRejectInput"
                    @click="processVerifikasi('REJECTED')" 
                    class="px-4 py-2 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg font-bold transition"
                    :disabled="isLoading"
                >
                    Tolak Akun
                </button>

                <button 
                    v-else
                    @click="processVerifikasi('REJECTED')" 
                    class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg font-bold transition shadow-lg shadow-red-200"
                    :disabled="isLoading"
                >
                    {{ isLoading ? 'Mengirim...' : 'Kirim Penolakan' }}
                </button>

                <button 
                    v-if="!showRejectInput"
                    @click="processVerifikasi('ACTIVE')" 
                    class="px-6 py-2 bg-green-500 text-white hover:bg-green-600 rounded-lg font-bold transition shadow-lg shadow-green-200"
                    :disabled="isLoading"
                >
                    {{ isLoading ? 'Memproses...' : 'Terima & Aktifkan' }}
                </button>
            </div>
        </div>
    </div>

  </div>
</template>

<style scoped>
.animate-fade-in { 
  animation: fadeIn 0.3s ease-out; 
}

@keyframes fadeIn {
  from { 
    opacity: 0; 
    transform: translateY(-10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>