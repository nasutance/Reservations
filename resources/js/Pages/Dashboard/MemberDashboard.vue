<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DataTable from '@/Components/DataTable.vue'
import { formatDate } from '@/utils/formatDate.js'

// Props Inertia
const reservations = usePage().props.reservations ?? []
const prices = usePage().props.prices ?? []

// Formattage des réservations
const formattedReservations = computed(() => {
  return reservations.map(resa => {
    const enrichedReps = (resa.representations ?? []).map(rep => ({
      ...rep,
      pivot: {
        ...rep.pivot,
        original_price_id: rep.pivot.price_id,
        original_quantity: rep.pivot.quantity,
      },
    }))

    const detail = enrichedReps
      .filter(rep => rep.pivot.quantity > 0)
      .map(rep => {
        const price = prices.find(p => p.id === rep.pivot.price_id)
        return price ? `${rep.pivot.quantity} ${price.type}` : `${rep.pivot.quantity} -`
      })
      .join('<br>')

    return {
      id: resa.id,
      user: resa.user ? `${resa.user.firstname} ${resa.user.lastname}` : '-',
      showTitle: enrichedReps[0]?.show?.title || '-',
      schedule: enrichedReps[0]?.schedule ? formatDate(enrichedReps[0].schedule, true) : '-',
      location: enrichedReps[0]?.location?.designation || '-',
      status: resa.status,
      detail,
      representations: enrichedReps
    }
  })
})

const headersResa = ['#', 'Spectacle', 'Représentation', 'Statut', 'Détail', 'Actions']
const fieldsResa = ['id', 'showTitle', 'schedule', 'status', 'detail', 'actions']

function updateStatus(id, status) {
  if (status === 'annulée' && !confirm('Confirmer l’annulation de cette réservation ?')) return

  router.visit(`/reservation/${id}`, {
    method: 'patch',
    data: { status },
    preserveScroll: true,
    preserveState: false // <- important ici
  })
}
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold text-gray-800 leading-tight mb-4">Mes réservations</h2>
    <div class="p-6 text-gray-900">
      <DataTable
        v-if="formattedReservations.length"
        :headers="headersResa"
        :fields="fieldsResa"
        :rows="formattedReservations"
      >
        <template #status="{ row }">
          <span class="capitalize font-semibold"
                :class="{
                  'text-yellow-600': row.status === 'en attente',
                  'text-green-600': row.status === 'payée',
                  'text-red-600': row.status === 'annulée'
                }">
            {{ row.status }}
          </span>
        </template>

        <template #detail="{ row }">
          <span v-html="row.detail" />
        </template>

        <template #actions="{ row }">
          <div class="flex gap-3">
            <button
              v-if="row.status === 'en attente'"
              @click="updateStatus(row.id, 'payée')"
              class="text-sm text-green-600 hover:underline"
            >
              💳 Payer
            </button>
            <button
              v-if="row.status !== 'annulée'"
              @click="updateStatus(row.id, 'annulée')"
              class="text-sm text-red-600 hover:underline"
            >
              ❌ Annuler
            </button>
          </div>
        </template>
      </DataTable>

      <p v-else class="mt-4 text-gray-500">
        Aucune réservation pour le moment.
      </p>
    </div>
  </div>
</template>
