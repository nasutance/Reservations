<template>
  <Head title="Mon espace" />

  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Mes réservations</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <table class="w-full text-sm text-left text-gray-700">
              <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b">
                <tr>
                  <th class="px-4 py-3">#</th>
                  <th class="px-4 py-3">Spectacle</th>
                  <th class="px-4 py-3">Représentation</th>
                  <th class="px-4 py-3">Statut</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="resa in reservations" :key="resa.id" class="border-b hover:bg-gray-50">
                  <td class="px-4 py-3 font-semibold">#{{ resa.id }}</td>
                  <td class="px-4 py-3">
                    {{ resa.representations[0]?.show?.title || '-' }}
                  </td>
                  <td class="px-4 py-3">
                    {{ formatDate(resa.representations[0]?.schedule) }}
                  </td>
                  <td class="px-4 py-3">
                    <span :class="statusClass(resa.status)">
                      {{ resa.status }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right">
                    <Link :href="route('reservation.show', resa.id)" class="text-indigo-600 hover:underline text-sm">
                      Voir détail
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-if="!reservations.length" class="mt-4 text-gray-500">Aucune réservation pour le moment.</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const reservations = usePage().props.reservations ?? []

function formatDate(date) {
  if (!date) return '-'
  return new Date(date).toLocaleString('fr-BE', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

function statusClass(status) {
  switch (status) {
    case 'payée':
      return 'text-green-600 font-semibold'
    case 'en attente':
      return 'text-yellow-600 font-semibold'
    case 'annulée':
      return 'text-red-600 font-semibold'
    default:
      return 'text-gray-600'
  }
}
</script>
