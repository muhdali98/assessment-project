import { CURRENCIES } from '../config'

export function formatCurrency(amount, currencyCode) {
  const currency = CURRENCIES.find(c => c.code === currencyCode)
  return `${currency?.symbol || ''}${parseFloat(amount).toFixed(2)}`
}

export function formatDate(dateString) {
  const date = new Date(dateString + 'T00:00:00')
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}