import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { formatDate } from '@/utils/formatDate.js'

export default function useFormattedReservations() {

  const reservations = usePage().props.reservations || []
  const prices = computed(() => usePage().props.prices ?? [])

  const formattedReservations = computed(() => {
    return reservations.map(resa => {
      const detail = resa.representations.map(rep => {
        const price = prices.value.find(p => p.id === rep.pivot.price_id)
        return price ? `${rep.pivot.quantity} ${price.type}` : `${rep.pivot.quantity} -`
      }).join('<br>')

      const originalTotal = resa.representations.reduce((total, rep) => {
        const price = prices.value.find(p => p.id === rep.pivot.price_id)
        return total + (rep.pivot.quantity * (price?.price ?? 0))
      }, 0)

      const enrichedReps = resa.representations.map(rep => ({
        ...rep,
        pivot: {
          ...rep.pivot,
          original_price_id: rep.pivot.price_id,
          original_quantity: rep.pivot.quantity
        }
      }))

      return {
        id: resa.id,
        user: resa.user ? `${resa.user.firstname} ${resa.user.lastname}` : '-',
        showTitle: resa.representations[0]?.show?.title || '-',
        schedule: resa.representations[0]?.schedule ? formatDate(resa.representations[0].schedule,true) : '-',
        location: resa.representations[0]?.location?.designation || '-',
        status: resa.status,
        detail,
        originalTotal,
        representations: enrichedReps
      }
    })
  })

  return { formattedReservations, prices }
}
