import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getPublicKategoriList,
  getPublicKategoriById,
  addKategori,
  updateKategori,
  deleteKategori,
  getErrorMessage
} from '@/services/api'

export const useKategoriStore = defineStore('kategori', () => {
  // State
  const kategoriList = ref([])
  const currentKategori = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Actions

  /**
   * Fetch all kategori (public)
   */
  async function fetchAllKategori() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicKategoriList()
      
      if (response.data && Array.isArray(response.data.data)) {
        kategoriList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        kategoriList.value = response.data
      } else {
        kategoriList.value = []
      }
      
      return kategoriList.value
    } catch (err) {
      console.error('Error fetching kategori:', err)
      error.value = getErrorMessage(err)
      
      // Fallback data
      kategoriList.value = [
        { id: 1, namaKategori: 'Elektronik' },
        { id: 2, namaKategori: 'Fashion' },
        { id: 3, namaKategori: 'Makanan' },
        { id: 4, namaKategori: 'Peralatan Rumah' },
        { id: 5, namaKategori: 'Olahraga' }
      ]
      
      return kategoriList.value
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch kategori by ID
   */
  async function fetchKategoriById(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicKategoriById(id)
      currentKategori.value = response.data
      return response.data
    } catch (err) {
      console.error(`Error fetching kategori with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create new kategori (Admin only)
   */
  async function createKategori(kategoriData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await addKategori(kategoriData)
      
      // Refresh list after adding
      await fetchAllKategori()
      
      return {
        success: true,
        message: 'Kategori berhasil ditambahkan',
        data: response.data
      }
    } catch (err) {
      console.error('Error creating kategori:', err)
      error.value = getErrorMessage(err)
      return {
        success: false,
        message: error.value
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Update kategori (Admin only)
   */
  async function editKategori(id, kategoriData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await updateKategori(id, kategoriData)
      
      // Refresh list after updating
      await fetchAllKategori()
      
      return {
        success: true,
        message: 'Kategori berhasil diperbarui',
        data: response.data
      }
    } catch (err) {
      console.error(`Error updating kategori with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return {
        success: false,
        message: error.value
      }
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Delete kategori (Admin only)
   */
  async function removeKategori(id) {
    isLoading.value = true
    error.value = null

    try {
      await deleteKategori(id)
      
      // Remove from local list
      kategoriList.value = kategoriList.value.filter(kategori => kategori.id !== id)
      
      return {
        success: true,
        message: 'Kategori berhasil dihapus'
      }
    } catch (err) {
      console.error(`Error deleting kategori with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return {
        success: false,
        message: error.value
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    // State
    kategoriList,
    currentKategori,
    isLoading,
    error,

    // Actions
    fetchAllKategori,
    fetchKategoriById,
    createKategori,
    editKategori,
    removeKategori
  }
})
