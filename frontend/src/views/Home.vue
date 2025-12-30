<template>
  <div class="home-view py-4">
    <div class="container">
      <!-- Header -->
     <header class="text-center mb-4">
        <h1 class="display-4 text-primary">Transaction</h1>
        <div class="d-flex justify-content-center flex-wrap gap-3 mt-3">

          <!-- Dashboard Button -->
          <router-link to="/dashboard" class="btn d-flex align-items-center gap-2"
            :class="[$route.name === 'Dashboard' ? 'btn-primary text-white' : 'btn-outline-primary']">
            <i class="bi bi-bar-chart-line"></i>
            Dashboard
          </router-link>
          <!-- Transactions Button -->
          <router-link to="/" class="btn d-flex align-items-center gap-2"
            :class="[$route.name === 'Home' ? 'btn-primary text-white' : 'btn-outline-primary']">
            <i class="bi bi-list-task"></i>
            Transactions
          </router-link>
        </div>

      </header>

      <!-- Transaction Form -->
      <div class="mb-4">
        <TransactionForm 
          :editing-transaction="editingTransaction"
          @transaction-saved="handleTransactionSaved"
          @cancel-edit="handleCancelEdit"
        />
      </div>

      <!-- <FilterBar 
        :filters="filters"
        @update:filters="handleFiltersUpdate"
        @export="exportToCSV"
      /> -->
      <!-- Transaction List -->
      <TransactionList 
        :transactions="transactions"
        :loading="loading"
        @edit="handleEdit"
        @delete="handleDelete"
       
      />
      
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import TransactionForm from '../components/TransactionForm.vue'
import TransactionList from '../components/TransactionList.vue'
import FilterBar from '../components/FilterBar.vue'
import { useTransactions } from '../composables/useTransactions'

export default {
  name: 'Home',
  components: {
    TransactionForm,
    TransactionList,
    FilterBar
  },
  setup() {
    const { transactions, loading, fetchTransactions, createTransaction, updateTransaction, deleteTransaction } = useTransactions()
    
    const editingTransaction = ref(null)
    const filters = ref({
      search: '',
      currency: '',
      category_id: '',
      date_from: '',
      date_to: ''
    })

    const filteredTransactions = computed(() => {
      let filtered = [...transactions.value]

      if (filters.value.search) {
        const search = filters.value.search.toLowerCase()
        filtered = filtered.filter(t => 
          t.title.toLowerCase().includes(search) || 
          (t.description && t.description.toLowerCase().includes(search))
        )
      }

      if (filters.value.currency) filtered = filtered.filter(t => t.currency === filters.value.currency)
      if (filters.value.category_id) filtered = filtered.filter(t => t.category_id === parseInt(filters.value.category_id))
      if (filters.value.date_from) filtered = filtered.filter(t => t.transaction_date >= filters.value.date_from)
      if (filters.value.date_to) filtered = filtered.filter(t => t.transaction_date <= filters.value.date_to)

      return filtered
    })

    const handleTransactionSaved = async (data) => {
      try {

        if (editingTransaction.value) {
          await updateTransaction(editingTransaction.value.id, data)
          editingTransaction.value = null
        } else {
          await createTransaction(data)
        }
        console.log('Transaction saved and local data updated!')
      } catch (err) {
        console.error('Error saving transaction:', err)
      }
    }

    const handleEdit = (transaction) => {
      editingTransaction.value = transaction
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }

    const handleCancelEdit = () => editingTransaction.value = null

    const handleDelete = async (id) => {
      await deleteTransaction(id)
      await fetchTransactions()
    }

    onMounted(() => fetchTransactions())

    return {
      transactions,
      filteredTransactions,
      loading,
      editingTransaction,
      filters,
      handleTransactionSaved,
      handleEdit,
      handleCancelEdit,
      handleDelete,
      fetchTransactions
    }
  }
}
</script>

<style scoped>
.home-view {
  background: #f8f9fa;
  min-height: 100vh;
}

/* header h1 {
  font-weight: 700;
} */

header p {
  margin-bottom: 1rem;
}

.nav-buttons .btn {
  min-width: 150px;
}

.nav-buttons .btn-primary {
  background-color: #667eea;
  border-color: #667eea;
}

.nav-buttons .btn-outline-light:hover {
  background-color: #667eea;
  color: #ffffff;
}
</style>
