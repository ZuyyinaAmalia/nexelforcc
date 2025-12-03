import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getPenjualProdukList,
  getPenjualProdukById,
  addPenjualProduk,
  updatePenjualProduk,
  deletePenjualProduk,
  uploadProdukGambar,
  getPublicProdukList,
  getPublicProdukById,
  getErrorMessage
} from '@/services/api'

export const useProdukStore = defineStore('produk', () => {
  // State
  const produkList = ref([])
  const currentProduk = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Actions

  /**
   * Fetch all produk (untuk penjual yang login)
   */
  async function fetchAllProduk() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualProdukList()
      
      if (response.data && Array.isArray(response.data.data)) {
        produkList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        produkList.value = response.data
      } else {
        produkList.value = []
      }
      
      return produkList.value
    } catch (err) {
      console.error('Error fetching produk:', err)
      error.value = getErrorMessage(err)
      
      // Fallback data
      produkList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch produk by ID
   */
  async function fetchProdukById(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualProdukById(id)
      currentProduk.value = response.data
      return response.data
    } catch (err) {
      console.error(`Error fetching produk with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create new produk
   */
  async function createProduk(produkData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await addPenjualProduk(produkData)
      
      // Refresh list after adding
      await fetchAllProduk()
      
      return {
        success: true,
        message: response.data.message || 'Produk berhasil ditambahkan',
        data: response.data.data
      }
    } catch (err) {
      console.error('Error creating produk:', err)
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
   * Update produk
   */
  async function editProduk(id, produkData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await updatePenjualProduk(id, produkData)
      
      // Refresh list after updating
      await fetchAllProduk()
      
      return {
        success: true,
        message: 'Produk berhasil diperbarui',
        data: response.data
      }
    } catch (err) {
      console.error(`Error updating produk with id ${id}:`, err)
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
   * Delete produk
   */
  async function removeProduk(id) {
    isLoading.value = true
    error.value = null

    try {
      await deletePenjualProduk(id)
      
      // Remove from local list
      produkList.value = produkList.value.filter(produk => produk.id !== id)
      
      return {
        success: true,
        message: 'Produk berhasil dihapus'
      }
    } catch (err) {
      console.error(`Error deleting produk with id ${id}:`, err)
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
   * Upload gambar produk
   */
  async function uploadGambar(file) {
    isLoading.value = true
    error.value = null

    try {
      const formData = new FormData()
      formData.append('gambar', file)

      const response = await uploadProdukGambar(formData)
      
      return {
        success: true,
        message: response.data.message || 'Gambar berhasil diupload',
        path: response.data.path,
        url: response.data.url
      }
    } catch (err) {
      console.error('Error uploading gambar:', err)
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
   * Fetch public produk list (tanpa auth)
   */
  async function fetchPublicProdukList() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicProdukList()
      
      if (response.data && Array.isArray(response.data.data)) {
        produkList.value = response.data.data
      } else if (Array.isArray(response.data)) {
        produkList.value = response.data
      } else {
        produkList.value = []
      }
      
      return produkList.value
    } catch (err) {
      console.error('Error fetching public produk:', err)
      error.value = getErrorMessage(err)
      produkList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch public produk by ID (tanpa auth)
   */
  async function fetchPublicProdukById(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicProdukById(id)
      currentProduk.value = response.data
      return response.data
    } catch (err) {
      console.error(`Error fetching public produk with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  return {
    // State
    produkList,
    currentProduk,
    isLoading,
    error,

    // Actions
    fetchAllProduk,
    fetchProdukById,
    createProduk,
    editProduk,
    removeProduk,
    uploadGambar,
    fetchPublicProdukList,
    fetchPublicProdukById
  }
})
