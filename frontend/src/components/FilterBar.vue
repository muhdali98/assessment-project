<template>
  <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="flex flex-col md:flex-row gap-4">
      <div class="flex-1">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input
            v-model="localFilters.search"
            @input="emitFilters"
            type="text"
            placeholder="Search transactions..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      <select
        v-model="localFilters.currency"
        @change="emitFilters"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
      >
        <option value="">All Currencies</option>
        <option v-for="curr in currencies" :key="curr.code" :value="curr.code">
          {{ curr.code }}
        </option>
      </select>

      <select
        v-model="localFilters.category_id"
        @change="emitFilters"
        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
      >
        <option value="">All Categories</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
          {{ cat.name }}
        </option>
      </select>

      <button
        @click="$emit('export')"
        class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center gap-2 whitespace-nowrap"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Export CSV
      </button>
    </div>

    <div v-if="showDateFilter" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
        <input
          v-model="localFilters.date_from"
          @change="emitFilters"
          type="date"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
        <input
          v-model="localFilters.date_to"
          @change="emitFilters"
          type="date"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
        />
      </div>
    </div>

    <button
      @click="showDateFilter = !showDateFilter"
      class="mt-4 text-blue-600 hover:text-blue-800 text-sm font-medium"
    >
      {{ showDateFilter ? 'Hide' : 'Show' }} Date Filter
    </button>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
import { CURRENCIES, CATEGORIES } from '../config'

export default {
  name: 'FilterBar',
  props: {
    filters: {
      type: Object,
      required: true
    }
  },
  emits: ['update:filters', 'export'],
  setup(props, { emit }) {
    const localFilters = ref({ ...props.filters })
    const showDateFilter = ref(false)
    const currencies = CURRENCIES
    const categories = CATEGORIES

    watch(() => props.filters, (newFilters) => {
      localFilters.value = { ...newFilters }
    }, { deep: true })

    const emitFilters = () => {
      emit('update:filters', localFilters.value)
    }

    return {
      localFilters,
      showDateFilter,
      currencies,
      categories,
      emitFilters
    }
  }
}
</script>