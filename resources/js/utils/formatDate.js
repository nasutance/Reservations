export function formatDate(date, options = { time: true }) {
  if (!date) return '-'

  const opts = options.time
    ? { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
    : { day: '2-digit', month: '2-digit', year: 'numeric' }

  return new Date(date).toLocaleString('fr-BE', opts)
}
