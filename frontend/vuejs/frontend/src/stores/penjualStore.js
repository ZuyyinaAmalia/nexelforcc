import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  penjualRegister,
  penjualLogin,
  penjualLogout,
  getPenjualProfile,
  updatePenjualProfile,
  getPenjualDashboardStats,
  getErrorMessage
} from '@/services/api'

export const usePenjualStore = defineStore('penjual', () => {
  // State
  const profile = ref(null)
  const dashboardStats = ref({
    total_produk: 0,
    total_penjualan: 0,
    pesanan_baru: 0,
    aktivitas_terbaru: []
  })
  const isLoading = ref(false)
  const error = ref(null)
  const isAuthenticated = ref(false)

  // Actions

  /**
   * Register penjual baru
   */
  async function register(formData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await penjualRegister(formData)
      return {
        success: true,
        message: response.data.message || 'Registrasi berhasil! Silakan tunggu verifikasi admin.',
        data: response.data
      }
    } catch (err) {
      console.error('Error registering penjual:', err)
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
   * Login penjual
   */
  async function login(credentials) {
    isLoading.value = true
    error.value = null

    try {
      const response = await penjualLogin(credentials)
      const { access_token, user } = response.data

      // Simpan token dan data ke localStorage
      localStorage.setItem('token', access_token)
      localStorage.setItem('role', 'penjual')
      localStorage.setItem('nama_penjual', user.namaPenjual)

      // Set state
      profile.value = user
      isAuthenticated.value = true

      return {
        success: true,
        message: 'Login berhasil!',
        user
      }
    } catch (err) {
      console.error('Error logging in:', err)
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
   * Logout penjual
   */
  async function logout() {
    isLoading.value = true
    error.value = null

    try {
      await penjualLogout()
    } catch (err) {
      console.error('Error logging out:', err)
    } finally {
      // Clear localStorage dan state
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      localStorage.removeItem('nama_penjual')
      profile.value = null
      isAuthenticated.value = false
      dashboardStats.value = {
        total_produk: 0,
        total_penjualan: 0,
        pesanan_baru: 0,
        aktivitas_terbaru: []
      }
      isLoading.value = false
    }
  }

  /**
   * Fetch profile penjual
   */
  async function fetchProfile() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualProfile()
      profile.value = response.data.data
      isAuthenticated.value = true
      return response.data.data
    } catch (err) {
      console.error('Error fetching profile:', err)
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
   * Update profile penjual
   */
  async function updateProfile(data) {
    isLoading.value = true
    error.value = null

    try {
      const response = await updatePenjualProfile(data)
      profile.value = response.data
      
      // Update localStorage jika nama berubah
      if (data.namaPenjual) {
        localStorage.setItem('nama_penjual', data.namaPenjual)
      }

      return {
        success: true,
        message: 'Profile berhasil diperbarui',
        data: response.data
      }
    } catch (err) {
      console.error('Error updating profile:', err)
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
   * Fetch dashboard statistics
   */
  async function fetchDashboardStats() {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPenjualDashboardStats()
      dashboardStats.value = response.data.data
      return response.data.data
    } catch (err) {
      console.error('Error fetching dashboard stats:', err)
      error.value = getErrorMessage(err)
      
      // Fallback data jika error
      dashboardStats.value = {
        total_produk: 0,
        total_penjualan: 0,
        pesanan_baru: 0,
        aktivitas_terbaru: []
      }
      return null
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
    isAuthenticated.value = !!(token && role === 'penjual')
    return isAuthenticated.value
  }

  return {
    // State
    profile,
    dashboardStats,
    isLoading,
    error,
    isAuthenticated,

    // Actions
    register,
    login,
    logout,
    fetchProfile,
    updateProfile,
    fetchDashboardStats,
    checkAuth
  }
})
