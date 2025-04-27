<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Espace Admin</h2>

    <div class="flex space-x-4 mb-8">
      <Button @click="activeSection = 'users'">Utilisateurs</Button>
      <Button @click="activeSection = 'reservations'">Réservations</Button>
      <Button @click="activeSection = 'shows'">Spectacles</Button>
      <Button @click="activeSection = 'artists'">Artistes</Button>
    </div>

    <div>
      <p v-if="activeSection === 'users'">Liste des utilisateurs ici</p>

      <div v-if="activeSection === 'reservations'">
        <h3 class="text-xl font-semibold mb-4">Liste des réservations</h3>
        <DataTable :headers="headers" :fields="fields" :rows="formattedReservations" />
      </div>

      <p v-if="activeSection === 'shows'">Liste des spectacles ici</p>
      <p v-if="activeSection === 'artists'">Liste des artistes ici</p>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Button from '@/Components/Button.vue'
import DataTable from '@/Components/DataTable.vue'
import { formatDate } from '@/utils'

const activeSection = ref('')

// Récupérer les props envoyées par Laravel
const reservations = usePage().props.reservations ?? []
const prices = usePage().props.prices ?? []

// Headers pour Admin Réservations
const headers = ['#', 'Utilisateur', 'Spectacle', 'Représentation', 'Statut', 'Détail']
const fields = ['id', 'user', 'showTitle', 'schedule', 'status', 'actions']

// Construction du tableau Admin
const formattedReservations = reservations.map(resa => ({
  id: resa.id,
  user: resa.user ? `${resa.user.firstname} ${resa.user.lastname}` : '-',
  showTitle: resa.representations[0]?.show?.title || '-',
  schedule: formatDate(resa.representations[0]?.schedule),
  status: resa.status,
  actions: resa.representations.length
    ? resa.representations
        .map(rep => {
          const price = prices.find(p => p.id === rep.pivot.price_id);
          return price ? `${rep.pivot.quantity} ${price.description}` : `${rep.pivot.quantity} -`;
        })
        .join('<br>')
    : '-',
}))
</script>
