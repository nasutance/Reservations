<template>
  <div>
    <h2 class="text-xl font-semibold text-gray-800 leading-tight mb-4">Mes réservations</h2>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">

            <DataTable
              :headers="['#', 'Spectacle', 'Représentation', 'Statut', 'Détail']"
              :fields="['id', 'showTitle', 'schedule', 'status', 'détail']"
              :rows="formattedReservations"
            />

            <p v-if="!reservations.length" class="mt-4 text-gray-500">
              Aucune réservation pour le moment.
            </p>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { formatDate } from '@/utils'
import DataTable from '@/Components/DataTable.vue'

const reservations = usePage().props.reservations ?? []
const prices = usePage().props.prices ?? []

// Préparation des données à afficher
const formattedReservations = reservations.map(resa => ({
  id: resa.id,
  showTitle: resa.representations[0]?.show?.title || '-',
  schedule: formatDate(resa.representations[0]?.schedule),
  status: resa.status,
  détail: resa.representations.length
    ? resa.representations
        .map(rep => {
          const price = prices.find(p => p.id === rep.pivot.price_id);
          return price ? `${rep.pivot.quantity} ${price.description}` : `${rep.pivot.quantity} -`;
        })
        .join('<br>')
    : '-',
}))
</script>
