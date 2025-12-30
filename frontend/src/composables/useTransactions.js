import { ref } from 'vue'
import axios from 'axios'
import { API_URL } from '../config'

export function useTransactions() {
  const transactions = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Fetch all transactions
  const fetchTransactions = async (filters = {}) => {
    loading.value = true
    error.value = null
    try {
      const params = new URLSearchParams()
      Object.keys(filters).forEach(key => {
        if (filters[key]) params.append(key, filters[key])
      })
      
      const response = await axios.get(`${API_URL}/transactions.php?${params}`)
      transactions.value = response.data.data || []
    } catch (err) {
      error.value = err.message
      console.error('Error fetching transactions:', err)
    } finally {
      loading.value = false
    }
  }

  // Create new transaction
  const createTransaction = async (data) => {
    loading.value = true
    error.value = null
    try {
      const response = await axios.post(`${API_URL}/transactions.php`, data)
      // Optimistically update local transactions
      await fetchTransactions()
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  // Update existing transaction
  const updateTransaction = async (id, data) => {
    loading.value = true
    error.value = null
    try {
      await axios.put(`${API_URL}/transactions.php`, { ...data, id })
      // Update local transaction
      const index = transactions.value.findIndex(t => t.id === id)
      if (index !== -1) transactions.value[index] = { ...transactions.value[index], ...data }
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  // Delete transaction
  const deleteTransaction = async (id) => {
    loading.value = true
    error.value = null
    try {
      await axios.delete(`${API_URL}/transactions.php?id=${id}`)
      // Remove from local transactions
      await fetchTransactions()
      return { success: true }
    } catch (err) {
      error.value = err.response?.data?.message || err.message
      return { success: false, error: error.value }
    } finally {
      loading.value = false
    }
  }

  return {
    transactions,
    loading,
    error,
    fetchTransactions,
    createTransaction,
    updateTransaction,
    deleteTransaction
  }
}
