import axios from 'axios'

// ===== AXIOS INSTANCE =====
export const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
})

// ===== REQUEST INTERCEPTOR (Auto Add Token) =====
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// ===== RESPONSE INTERCEPTOR (Handle Errors) =====
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Handle 401 Unauthorized (Token expired atau tidak valid)
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      localStorage.removeItem('nama_penjual')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// =========================================================================
// PUBLIC API (Tidak perlu authentication)
// =========================================================================

// ----- PRODUK (Public) -----
export const getPublicProdukList = () => api.get('/public/produks')
export const getPublicProdukById = (id) => api.get(`/public/produks/${id}`)

// ----- KATEGORI (Public) -----
export const getPublicKategoriList = () => api.get('/public/kategoris')
export const getPublicKategoriById = (id) => api.get(`/public/kategoris/${id}`)

// ----- REVIEW (Public) -----
export const getPublicReviewList = (params) => api.get('/public/reviews', { params })
export const getPublicReviewByProdukId = (produkId) => api.get(`/public/reviews/product/${produkId}`)
export const getPublicReviewById = (id) => api.get(`/public/reviews/${id}`)
export const addPublicReview = (data) => api.post('/public/reviews', data)

// =========================================================================
// ADMIN API (Perlu auth: Bearer token Admin)
// =========================================================================

// ----- ADMIN AUTH -----
export const adminLogin = (data) => api.post('/admin/login', data)
export const adminLogout = () => api.post('/admin/logout')
export const getAdminProfile = () => api.get('/admin/me')

// ----- VERIFIKASI PENJUAL (Admin) -----
export const getPendingPenjualList = () => api.get('/admin/verifikasi-penjual')
export const verifikasiPenjual = (id, data) => api.post(`/admin/verifikasi-penjual/${id}`, data)

// ----- KELOLA KATEGORI (Admin) -----
export const addKategori = (data) => api.post('/admin/kategoris', data)
export const updateKategori = (id, data) => api.put(`/admin/kategoris/${id}`, data)
export const deleteKategori = (id) => api.delete(`/admin/kategoris/${id}`)

// ----- MODERASI REVIEW (Admin) -----
export const updateReview = (id, data) => api.put(`/admin/reviews/${id}`, data)
export const deleteReview = (id) => api.delete(`/admin/reviews/${id}`)

// ----- KELOLA PENJUAL (Admin CRUD) -----
export const getPenjualList = () => api.get('/admin/penjuals')
export const getPenjualById = (id) => api.get(`/admin/penjuals/${id}`)
export const addPenjual = (data) => api.post('/admin/penjuals', data)
export const updatePenjual = (id, data) => api.put(`/admin/penjuals/${id}`, data)
export const deletePenjual = (id) => api.delete(`/admin/penjuals/${id}`)

// =========================================================================
// PENJUAL API (Perlu auth: Bearer token Penjual)
// =========================================================================

// ----- PENJUAL AUTH -----
export const penjualRegister = (data) => api.post('/penjual/register', data)
export const penjualLogin = (data) => api.post('/penjual/login', data)
export const penjualLogout = () => api.post('/penjual/logout')

// ----- PENJUAL PROFILE -----
export const getPenjualProfile = () => api.get('/penjual/profile')
export const updatePenjualProfile = (data) => api.put('/penjual/profile', data)

// ----- PENJUAL DASHBOARD -----
export const getPenjualDashboardStats = () => api.get('/penjual/dashboard/stats')

// ----- KELOLA PRODUK PENJUAL -----
export const getPenjualProdukList = () => api.get('/penjual/produk')
export const getPenjualProdukById = (id) => api.get(`/penjual/produk/${id}`)
export const addPenjualProduk = (data) => api.post('/penjual/produk', data)
export const updatePenjualProduk = (id, data) => api.put(`/penjual/produk/${id}`, data)
export const deletePenjualProduk = (id) => api.delete(`/penjual/produk/${id}`)
export const uploadProdukGambar = (formData) => 
  api.post('/penjual/produk/upload-gambar', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  })

// ----- KELOLA ALAMAT PENJUAL -----
export const getPenjualAlamatList = () => api.get('/penjual/alamat')
export const getPenjualAlamatById = (id) => api.get(`/penjual/alamat/${id}`)
export const addPenjualAlamat = (data) => api.post('/penjual/alamat', data)
export const updatePenjualAlamat = (id, data) => api.put(`/penjual/alamat/${id}`, data)
export const deletePenjualAlamat = (id) => api.delete(`/penjual/alamat/${id}`)

// =========================================================================
// HELPER FUNCTIONS
// =========================================================================

// Format error message dari response
export const getErrorMessage = (error) => {
  if (error.response?.data?.message) {
    return error.response.data.message
  }
  if (error.response?.data?.errors) {
    const errors = error.response.data.errors
    return Object.values(errors).flat().join(', ')
  }
  return error.message || 'Terjadi kesalahan pada server'
}

// Check if user is authenticated
export const isAuthenticated = () => {
  return !!localStorage.getItem('token')
}

// Get current user role
export const getUserRole = () => {
  return localStorage.getItem('role')
}

// Clear auth data
export const clearAuth = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  localStorage.removeItem('nama_penjual')
}

export default api 