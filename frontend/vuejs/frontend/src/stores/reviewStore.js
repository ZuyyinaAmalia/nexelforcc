import { defineStore } from 'pinia'
import { ref } from 'vue'
import {
  getPublicReviewList,
  getPublicReviewByProdukId,
  getPublicReviewById,
  addPublicReview,
  updateReview,
  deleteReview,
  getErrorMessage
} from '@/services/api'

export const useReviewStore = defineStore('review', () => {
  // State
  const reviewList = ref([])
  const currentReview = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Actions

  /**
   * Fetch all reviews (optional filter by produk_id)
   */
  async function fetchAllReviews(produkId = null) {
    isLoading.value = true
    error.value = null

    try {
      const params = produkId ? { product_id: produkId } : {}
      const response = await getPublicReviewList(params)
      
      if (response.data && response.data.status === 'success') {
        reviewList.value = response.data.data || []
      } else if (Array.isArray(response.data)) {
        reviewList.value = response.data
      } else {
        reviewList.value = []
      }
      
      return reviewList.value
    } catch (err) {
      console.error('Error fetching reviews:', err)
      error.value = getErrorMessage(err)
      reviewList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch reviews by produk ID
   */
  async function fetchReviewsByProdukId(produkId) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicReviewByProdukId(produkId)
      
      if (response.data && response.data.status === 'success') {
        reviewList.value = response.data.data || []
      } else if (Array.isArray(response.data)) {
        reviewList.value = response.data
      } else {
        reviewList.value = []
      }
      
      return reviewList.value
    } catch (err) {
      console.error(`Error fetching reviews for produk ${produkId}:`, err)
      error.value = getErrorMessage(err)
      reviewList.value = []
      return []
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch review by ID
   */
  async function fetchReviewById(id) {
    isLoading.value = true
    error.value = null

    try {
      const response = await getPublicReviewById(id)
      currentReview.value = response.data
      return response.data
    } catch (err) {
      console.error(`Error fetching review with id ${id}:`, err)
      error.value = getErrorMessage(err)
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create new review
   */
  async function createReview(reviewData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await addPublicReview(reviewData)
      
      // Refresh list after adding
      if (reviewData.produk_id) {
        await fetchReviewsByProdukId(reviewData.produk_id)
      }
      
      return {
        success: true,
        message: response.data.message || 'Review berhasil ditambahkan',
        data: response.data.data
      }
    } catch (err) {
      console.error('Error creating review:', err)
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
   * Update review (Admin only)
   */
  async function editReview(id, reviewData) {
    isLoading.value = true
    error.value = null

    try {
      const response = await updateReview(id, reviewData)
      
      // Update in local list
      const index = reviewList.value.findIndex(review => review.id === id)
      if (index !== -1) {
        reviewList.value[index] = response.data.data
      }
      
      return {
        success: true,
        message: response.data.message || 'Review berhasil diperbarui',
        data: response.data.data
      }
    } catch (err) {
      console.error(`Error updating review with id ${id}:`, err)
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
   * Delete review (Admin only)
   */
  async function removeReview(id) {
    isLoading.value = true
    error.value = null

    try {
      await deleteReview(id)
      
      // Remove from local list
      reviewList.value = reviewList.value.filter(review => review.id !== id)
      
      return {
        success: true,
        message: 'Review berhasil dihapus'
      }
    } catch (err) {
      console.error(`Error deleting review with id ${id}:`, err)
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
   * Calculate average rating for a product
   */
  function getAverageRating() {
    if (reviewList.value.length === 0) return 0
    
    const totalRating = reviewList.value.reduce((sum, review) => sum + review.rating, 0)
    return (totalRating / reviewList.value.length).toFixed(1)
  }

  /**
   * Get rating distribution
   */
  function getRatingDistribution() {
    const distribution = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 }
    
    reviewList.value.forEach(review => {
      if (review.rating >= 1 && review.rating <= 5) {
        distribution[review.rating]++
      }
    })
    
    return distribution
  }

  return {
    // State
    reviewList,
    currentReview,
    isLoading,
    error,

    // Actions
    fetchAllReviews,
    fetchReviewsByProdukId,
    fetchReviewById,
    createReview,
    editReview,
    removeReview,
    getAverageRating,
    getRatingDistribution
  }
})
