<template>
  <div class="container my-4">
    <!-- Header -->
   <header class="text-center mb-4">
      <h1 class="display-4 text-primary">Dashboard</h1>
      <p class="lead text-muted">Manage your transactions efficiently</p>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-12 col-md-4 mb-3" v-for="card in cards" :key="card.title">
        <div class="card text-center shadow-sm h-100">
          <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">{{ card.title }}</h6>
            <div class="d-flex justify-content-center align-items-center mt-2">
              <i :class="card.icon + ' fs-3 me-2'" :style="{ color: card.color }"></i>
              <span class="fs-3 fw-bold">{{ card.value }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row">
      <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h6 class="card-title">Spending by Category</h6>
            <canvas id="categoryChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
          <div class="card-body">
            <h6 class="card-title">Spending by Currency</h6>
            <canvas id="currencyChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, computed, watch } from 'vue'
import { Chart, registerables } from 'chart.js'
import { useTransactions } from '../composables/useTransactions'
import { CATEGORIES } from '../config'

Chart.register(...registerables)

export default {
  name: 'Dashboard',
  setup() {
    const { transactions, fetchTransactions } = useTransactions()
    
    let categoryChart = null
    let currencyChart = null

    const totalTransactions = computed(() => transactions.value.length)
    const totalAmount = computed(() => transactions.value.reduce((sum, t) => sum + parseFloat(t.amount), 0))
    const avgTransaction = computed(() => totalTransactions.value ? (totalAmount.value / totalTransactions.value).toFixed(2) : 0)

    const cards = computed(() => [
      { title: 'Total Transactions', value: totalTransactions.value, icon: 'bi bi-list-task', color: '#0d6efd' },
      { title: 'Total Amount', value: `$${totalAmount.value.toFixed(2)}`, icon: 'bi bi-currency-dollar', color: '#198754' },
      { title: 'Average Transaction', value: `$${avgTransaction.value}`, icon: 'bi bi-bar-chart-line', color: '#6f42c1' },
    ])

    const renderCategoryChart = () => {
      const ctx = document.getElementById('categoryChart')
      if (!ctx) return
      if (categoryChart) categoryChart.destroy()

      const data = CATEGORIES.map(c => ({
        name: c.name,
        total: transactions.value.filter(t => t.category_id === c.id).reduce((sum, t) => sum + parseFloat(t.amount), 0),
        color: c.color
      })).filter(d => d.total > 0)

      categoryChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: data.map(d => d.name),
          datasets: [{ data: data.map(d => d.total), backgroundColor: data.map(d => d.color) }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
      })
    }

    const renderCurrencyChart = () => {
      const ctx = document.getElementById('currencyChart')
      if (!ctx) return
      if (currencyChart) currencyChart.destroy()

      const currencies = ['USD', 'EUR', 'GBP', 'MYR']
      const data = currencies.map(c => ({
        currency: c,
        total: transactions.value.filter(t => t.currency === c).reduce((sum, t) => sum + parseFloat(t.amount), 0)
      })).filter(d => d.total > 0)

      currencyChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.map(d => d.currency),
          datasets: [{ label: 'Total Amount', data: data.map(d => d.total), backgroundColor: '#0d6efd' }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
      })
    }

    onMounted(async () => {
      await fetchTransactions()
      renderCategoryChart()
      renderCurrencyChart()
    })

    watch(transactions, () => {
      renderCategoryChart()
      renderCurrencyChart()
    }, { deep: true })

    return { cards }
  }
}
</script>

<style scoped>
.card i {
  font-size: 1.5rem;
}
</style>
