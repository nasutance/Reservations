<template>
  <div>
    <h2 class="text-xl font-semibold text-gray-800 leading-tight mb-4">Mes réservations</h2>
    <div class="p-6 text-gray-900">
      <DataTable
        v-if="filteredReservations.length"
        :headers="headersResa"
        :fields="fieldsResa"
        :rows="filteredReservations"
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

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DataTable from '@/Components/DataTable.vue'
import useFormattedReservations from '@/utils/useFormattedReservations'

const headersResa = ['#', 'Spectacle', 'Représentation', 'Statut', 'Détail', 'Actions']
const fieldsResa = ['id', 'showTitle', 'schedule', 'status', 'detail', 'actions']

const { formattedReservations, prices } = useFormattedReservations()

const filteredReservations = computed(() =>
  formattedReservations.value.map(resa => ({
    ...resa,
    detail: resa.representations
      .filter(rep => rep.pivot.quantity > 0)
      .map(rep => {
        const price = prices.value.find(p => p.id === rep.pivot.price_id)
        return price ? `${rep.pivot.quantity} ${price.type}` : `${rep.pivot.quantity} -`
      })
      .join('<br>')
  }))
)

function updateStatus(id, status) {
  if (status === 'annulée' && !confirm('Confirmer l’annulation de cette réservation ?')) return

  router.visit(`/reservation/${id}`, {
    method: 'patch',
    data: { status },
    preserveScroll: true,
    preserveState: false // <= TRÈS important ici
  })


}
</script>
