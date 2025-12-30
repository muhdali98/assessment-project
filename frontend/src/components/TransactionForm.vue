<template>
  <div class="container my-4">
    <h2 class="mb-3">{{ editingTransaction ? 'Edit Transaction' : 'Add Transaction' }}</h2>
    <form @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input v-model="form.title" type="text" class="form-control" placeholder="Transaction Title" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Amount</label>
        <input v-model.number="form.amount" type="number" class="form-control" step="0.01" placeholder="Amount" required />
      </div>

      <div class="mb-3">
        <label class="form-label">Currency</label>
        <select v-model="form.currency" class="form-select" required>
          <option value="">Select Currency</option>
          <option value="USD">USD</option>
          <option value="EUR">EUR</option>
          <option value="MYR">MYR</option>
          <option value="GBP">GBP</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select v-model="form.category_id" class="form-select">
          <option value="">Select Category</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea v-model="form.description" class="form-control" placeholder="Optional description"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Date</label>
        <input v-model="form.transaction_date" type="date" class="form-control" required />
      </div>

      <button type="submit" class="btn btn-primary me-2">{{ editingTransaction ? 'Update' : 'Add' }}</button>
      <button type="button" @click="cancelEdit" class="btn btn-secondary">Cancel</button>

      <div v-if="error" class="text-danger mt-2">{{ error }}</div>
    </form>
  </div>
</template>


<script>
import { ref, watch, onMounted } from 'vue'
import { useTransactions } from '../composables/useTransactions'
import { useCategories } from '../composables/useCategories'

export default {
  name: 'TransactionForm',
  props: {
    editingTransaction: Object
  },
  emits: ['cancel-edit','transaction-saved'],
  setup(props, { emit }) {
    const { createTransaction, updateTransaction, error } = useTransactions()
    const { categories, fetchCategories } = useCategories()

    const form = ref({
      title: '',
      amount: '',
      currency: '',
      category_id: '',
      description: '',
      transaction_date: ''
    })

    const handleSubmit = async () => {
      emit('transaction-saved', {
        ...form.value,
        id: props.editingTransaction?.id || null
      })

      resetForm()
      emit('cancel-edit')
    }
    const cancelEdit = () => {
      resetForm()
      emit('cancel-edit')
    }

    const resetForm = () => {
      form.value = {
        title: '',
        amount: '',
        currency: '',
        category_id: '',
        description: '',
        transaction_date: ''
      }
    }

    watch(() => props.editingTransaction, (newVal) => {
      if (newVal) form.value = { ...newVal }
    }, { immediate: true })

    onMounted(() => fetchCategories())

    return {
      form,
      categories,
      handleSubmit,
      cancelEdit,
      error
    }
  }
}
</script>

<style scoped>
</style>
