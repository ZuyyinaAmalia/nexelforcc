import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  adminLogin,
  adminLogout,
  getAdminProfile,
  getPendingPenjualList,
  verifikasiPenjual,
  getErrorMessage
} from '@/services/api'

export const useAdminStore = defineStore('admin', () => {
  // State
  const profile = ref(null)
  const pendingPenjualList = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  const isAuthenticated = ref(false)

  // Actions

  /**
   * Login admin
   */
  async function login(credentials) {
    isLoading.value = true
    error.value = null

    try {
      const response = await adminLogin(credentials)
      const { token, admin } = response.data

      // Simpan token dan data ke localStorage
      localStorage.setItem('token', token)
      localStorage.setItem('role', 'admin')

      // Set state
      profile.value = admin
      isAuthenticated.value = true

      return {
        success: true,
        message: 'Login berhasil!',
        admin
      }
    } catch (err) {
      console.error('Error logging in admin:', err)
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
   * Logout admin
   */
  async function logout() {
    isLoading.value = true
    error.value = null

    try {
      await adminLogout()
    } catch (err) {
      console.error('Error logging out admin:', err)
    } finally {
      // Clear localStorage dan state
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      profile.value = null
      isAuthenticated.value = false
      pendingPenjualList.value = []
      isLoading.value = false
    }
  }

  /**
   * Fetch admin profile
   */
  async function fetchProfile() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getAdminProfile()
      profile.value = response.data
      isAuthenticated.value = true
      return response.data
    } catch (err) {
      console.error('Error fetching admin profile:', err)
      error.value = getErrorMessage(err)
      
      // Jika error 401, logout
      if (err.response?.status === 401) {
        await logout()
      }
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch pending penjual list (for verification)
   */
  async function fetchPendingPenjual() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPendingPenjualList()
      
      if (Array.isArray(response.data)) {
        pendingPenjualList.value = response.data
      } else if (response.data && Array.isArray(response.data.data)) {
        pendingPenjualList.value = response.data.data
      } else {
        pendingPenjualList.value = []
      }
      
      return pendingPenjualList.value
    } catch (err) {
      console.error('Error fetching pending penjual:', err)
      error.value = getErrorMessage(err)
      pendingPenjualList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Verifikasi penjual (ACTIVE or REJECTED)
   */
  async function verifyPenjual(id, status) {
    isLoading.value = true
    error.value = null

    try {
      const response = await verifikasiPenjual(id, { status })
      
      // Remove from pending list
      pendingPenjualList.value = pendingPenjualList.value.filter(
        penjual => penjual.id !== id
      )
      
      return {
        success: true,
        message: response.data.message || `Penjual berhasil di${status === 'ACTIVE' ? 'terima' : 'tolak'}`,
        data: response.data.data
      }
    } catch (err) {
      console.error(`Error verifying penjual with id ${id}:`, err)
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
   * Check authentication status
   */
  function checkAuth() {
    const token = localStorage.getItem('token')
    const role = localStorage.getItem('role')
    isAuthenticated.value = !!(token && role === 'admin')
    return isAuthenticated.value
  }

  return {
    // State
    profile,
    pendingPenjualList,
    isLoading,
    error,
    isAuthenticated,

    // Actions
    login,
    logout,
    fetchProfile,
    fetchPendingPenjual,
    verifyPenjual,
    checkAuth
  }
})
