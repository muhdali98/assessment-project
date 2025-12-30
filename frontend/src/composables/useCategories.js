import { ref } from 'vue'
import axios from 'axios'
import { API_URL } from '../config'

export function useCategories() {
  const categories = ref([])
  const loading = ref(false)

  const fetchCategories = async () => {
    loading.value = true
    try {
      const response = await axios.get(`${API_URL}/categories.php`)
      categories.value = response.data.data
    } catch (err) {
      console.error('Error fetching categories:', err)
    } finally {
      loading.value = false
    }
  }

  return {
    categories,
    loading,
    fetchCategories
  }
}