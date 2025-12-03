<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

interface Kategori {
  id: number
  namaKategori: string
  created_at: string
  updated_at: string
}

const kategoris = ref<Kategori[]>([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentKategori = ref<Kategori | null>(null)

const formData = ref({
  namaKategori: ''
})

const errors = ref<{ namaKategori?: string }>({})

// Fetch semua kategori
const fetchKategoris = async () => {
  loading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/kategoris')
    kategoris.value = response.data.data
  } catch (error) {
    console.error('Error fetching kategoris:', error)
    alert('Gagal memuat data kategori')
  } finally {
    loading.value = false
  }
}

// Buka modal untuk tambah kategori
const openAddModal = () => {
  isEditing.value = false
  formData.value.namaKategori = ''
  errors.value = {}
  showModal.value = true
}

// Buka modal untuk edit kategori
const openEditModal = (kategori: Kategori) => {
  isEditing.value = true
  currentKategori.value = kategori
  formData.value.namaKategori = kategori.namaKategori
  errors.value = {}
  showModal.value = true
}

// Tutup modal
const closeModal = () => {
  showModal.value = false
  formData.value.namaKategori = ''
  errors.value = {}
  currentKategori.value = null
}

// Submit form (Create atau Update)
const submitForm = async () => {
  errors.value = {}
  
  if (!formData.value.namaKategori.trim()) {
    errors.value.namaKategori = 'Nama kategori harus diisi'
    return
  }

  loading.value = true
  try {
    if (isEditing.value && currentKategori.value) {
      // Update
      await axios.put(
        `http://localhost:8000/api/kategoris/${currentKategori.value.id}`,
        formData.value
      )
      alert('Kategori berhasil diupdate!')
    } else {
      // Create
      await axios.post('http://localhost:8000/api/kategoris', formData.value)
      alert('Kategori berhasil ditambahkan!')
    }
    
    closeModal()
    fetchKategoris()
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else if (error.response?.data?.message) {
      alert(error.response.data.message)
    } else {
      alert('Terjadi kesalahan')
    }
  } finally {
    loading.value = false
  }
}

// Hapus kategori
const deleteKategori = async (id: number) => {
  if (!confirm('Yakin ingin menghapus kategori ini?')) return
  
  loading.value = true
  try {
    await axios.delete(`http://localhost:8000/api/kategoris/${id}`)
    alert('Kategori berhasil dihapus!')
    fetchKategoris()
  } catch (error) {
    console.error('Error deleting kategori:', error)
    alert('Gagal menghapus kategori')
  } finally {
    loading.value = false
  }
}

// Format tanggal
const formatDate = (dateString: string) => {
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }
  return new Date(dateString).toLocaleDateString('id-ID', options)
}

onMounted(() => {
  fetchKategoris()
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>
          <p class="text-gray-500 text-sm mt-1">Tambah, edit, dan hapus kategori produk</p>
        </div>
        <button 
          @click="openAddModal"
          class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors flex items-center gap-2"
        >
          <span class="text-lg">➕</span>
          Tambah Kategori
        </button>
      </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">
        Loading...
      </div>
      
      <div v-else-if="kategoris.length === 0" class="p-8 text-center text-gray-500">
        Belum ada kategori. Silakan tambahkan kategori baru.
      </div>

      <table v-else class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kategori</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat Pada</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="kategori in kategoris" :key="kategori.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-900">{{ kategori.id }}</td>
            <td class="px-6 py-4">
              <span class="text-sm font-medium text-gray-900">{{ kategori.namaKategori }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(kategori.created_at) }}</td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <button 
                  @click="openEditModal(kategori)"
                  class="text-blue-600 hover:text-blue-800 font-medium text-sm px-3 py-1 rounded hover:bg-blue-50 transition-colors"
                >
                  Edit
                </button>
                <button 
                  @click="deleteKategori(kategori.id)"
                  class="text-red-600 hover:text-red-800 font-medium text-sm px-3 py-1 rounded hover:bg-red-50 transition-colors"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-800">
            {{ isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
          </h2>
        </div>
        
        <form @submit.prevent="submitForm" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Kategori <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="formData.namaKategori"
              type="text" 
              placeholder="Contoh: Elektronik, Fashion, dll"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all"
              :class="{ 'border-red-500': errors.namaKategori }"
            />
            <p v-if="errors.namaKategori" class="text-red-500 text-sm mt-1">
              {{ Array.isArray(errors.namaKategori) ? errors.namaKategori[0] : errors.namaKategori }}
            </p>
          </div>

          <div class="flex gap-3 pt-4">
            <button 
              type="button"
              @click="closeModal"
              class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors"
              :disabled="loading"
            >
              Batal
            </button>
            <button 
              type="submit"
              class="flex-1 px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium transition-colors disabled:opacity-50"
              :disabled="loading"
            >
              {{ loading ? 'Menyimpan...' : (isEditing ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>