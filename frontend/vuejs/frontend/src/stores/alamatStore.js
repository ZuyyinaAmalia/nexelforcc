import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getPenjualAlamatList,
  getPenjualAlamatById,
  addPenjualAlamat,
  updatePenjualAlamat,
  deletePenjualAlamat,
  getErrorMessage
} from '@/services/api'

export const useAlamatStore = defineStore('alamat', () => {
  // State
  const alamatList = ref([])
  const currentAlamat = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Actions

  /**
   * Fetch all alamat penjual
   */
  async function fetchAllAlamat() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualAlamatList()
      
      if (response.data && response.data.success) {
        alamatList.value = response.data.data || []
      } else if (Array.isArray(response.data)) {
        alamatList.value = response.data
      } else {
        alamatList.value = []
      }
      
      return alamatList.value
    } catch (err) {
      console.error('Error fetching alamat:', err)
      error.value = getErrorMessage(err)
      alamatList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch alamat by ID
   */
  async function fetchAlamatById(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualAlamatById(id)
      
      if (response.data && response.data.success) {
        currentAlamat.value = response.data.data
        return response.data.data
      } else {
        currentAlamat.value = response.data
        return response.data
      }
    } catch (err) {
      console.error(`Error fetching alamat with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create new alamat
   */
  async function createAlamat(alamatData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await addPenjualAlamat(alamatData)
      
      // Refresh list after adding
      await fetchAllAlamat()
      
      return {
        success: true,
        message: response.data.message || 'Alamat berhasil ditambahkan',
        data: response.data.data
      }
    } catch (err) {
      console.error('Error creating alamat:', err)
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
   * Update alamat
   */
  async function editAlamat(id, alamatData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await updatePenjualAlamat(id, alamatData)
      
      // Refresh list after updating
      await fetchAllAlamat()
      
      return {
        success: true,
        message: response.data.message || 'Alamat berhasil diperbarui',
        data: response.data.data
      }
    } catch (err) {
      console.error(`Error updating alamat with id ${id}:`, err)
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
   * Delete alamat
   */
  async function removeAlamat(id) {
    isLoading.value = true
    error.value = null

    try {
      await deletePenjualAlamat(id)
      
      // Remove from local list
      alamatList.value = alamatList.value.filter(alamat => alamat.id !== id)
      
      return {
        success: true,
        message: 'Alamat berhasil dihapus'
      }
    } catch (err) {
      console.error(`Error deleting alamat with id ${id}:`, err)
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
   * Format alamat lengkap
   */
  function formatAlamatLengkap(alamat) {
    if (!alamat) return ''
    
    const parts = [
      alamat.jalan,
      alamat.rt ? `RT ${alamat.rt}` : null,
      alamat.rw ? `RW ${alamat.rw}` : null,
      alamat.desa,
      alamat.kota,
      alamat.provinsi
    ].filter(Boolean)
    
    return parts.join(', ')
  }

  return {
    // State
    alamatList,
    currentAlamat,
    isLoading,
    error,

    // Actions
    fetchAllAlamat,
    fetchAlamatById,
    createAlamat,
    editAlamat,
    removeAlamat,
    formatAlamatLengkap
  }
})
