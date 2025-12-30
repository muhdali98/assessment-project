export function validateTransaction(data) {
  const errors = []
  
  if (!data.title || data.title.trim() === '') {
    errors.push('Title is required')
  }
  
  if (!data.amount || parseFloat(data.amount) <= 0) {
    errors.push('Valid amount is required')
  }
  
  if (!data.currency) {
    errors.push('Currency is required')
  }
  
  if (!data.transaction_date) {
    errors.push('Transaction date is required')
  }
  
  return errors
}