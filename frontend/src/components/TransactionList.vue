<template>
  <div class="container my-4">
    <h2 class="mb-3">Transactions</h2>
    <table class="table table-striped table-bordered" v-if="transactions.length">
      <thead class="table-dark">
        <tr>
          <th>Title</th>
          <th>Amount</th>
          <th>Currency</th>
          <th>Category</th>
          <th>Description</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="t in transactions" :key="t?.id">
          <td>{{ t.title }}</td>
          <td>{{ t.amount }}</td>
          <td>{{ t.currency }}</td>
          <td>{{ t.category_name || 'N/A' }}</td>
          <td>{{ t.description }}</td>
          <td>{{ t.transaction_date }}</td>
          <td>
            <button @click="editTransaction(t)" class="btn btn-sm btn-warning me-1">Edit</button>
            <button @click="removeTransaction(t.id)" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-else class="text-muted">No transactions found.</p>
    <div v-if="error" class="text-danger">{{ error }}</div>
  </div>
</template>


<script>
import { useTransactions } from '../composables/useTransactions'

export default {
  name: 'TransactionList',
  props: {
    transactions: Array
  },
  emits: ['edit','delete'],
  setup(props, { emit }) {
    const { fetchTransactions,deleteTransaction, error } = useTransactions()

    const editTransaction = (t) => {
      emit('edit', t)
    }

    const removeTransaction = async (id) => {
      if (confirm('Are you sure you want to delete this transaction?')) {
        emit('delete', id)
      }
    }

    return {
      editTransaction,
      removeTransaction,
      error
    }
  }
}
</script>

<style scoped>
</style>
