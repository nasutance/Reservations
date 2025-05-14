<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DataTable from '@/Components/DataTable.vue'
import { formatDate } from '@/utils/formatDate.js'

// Récupère les données envoyées par le backend via Inertia
const reservations = usePage().props.reservations ?? []  // Liste des réservations
const prices = usePage().props.prices ?? []              // Liste des tarifs disponibles

// Formatage enrichi des données de réservation
const formattedReservations = computed(() => {
  return reservations.map(resa => {
    // On enrichit chaque représentation avec un champ pivot cloné (copie de prix/quantité)
    const enrichedReps = (resa.representations ?? []).map(rep => ({
      ...rep,
      pivot: {
        ...rep.pivot,
        original_price_id: rep.pivot.price_id,
        original_quantity: rep.pivot.quantity,
      },
    }))

    // Construction du détail textuel pour affichage HTML
    const detail = enrichedReps
      .filter(rep => rep.pivot.quantity > 0)
      .map(rep => {
        const price = prices.find(p => p.id === rep.pivot.price_id)
        return {
          quantity: rep.pivot.quantity,
          type: price?.type || '-'
        }
      })

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

// En-têtes et champs à afficher dans le tableau
const headersResa = ['#', 'Spectacle', 'Représentation', 'Statut', 'Détail', 'Actions']
const fieldsResa = ['id', 'showTitle', 'schedule', 'status', 'detail', 'actions']

// Fonction pour changer le statut d'une réservation (payée ou annulée)
function updateStatus(id, status) {
  if (status === 'annulée' && !confirm('Confirmer l’annulation de cette réservation ?')) return

  router.visit(`/reservation/${id}`, {
    method: 'patch',
    data: { status },
    preserveScroll: true,
    preserveState: false // On veut rafraîchir les données complètement
  })
}
</script>

<template>
  <div>
    <!-- Titre principal -->
    <h2 class="text-xl font-semibold text-gray-800 leading-tight mb-4">Mes réservations</h2>

    <div class="p-6 text-gray-900">
      <!-- Affichage du tableau uniquement si des données existent -->
      <DataTable
        v-if="formattedReservations.length"
        :headers="headersResa"
        :fields="fieldsResa"
        :rows="formattedReservations"
      >

        <!-- Affichage stylisé du statut (couleur conditionnelle) -->
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

        <!-- Détail des billets par type (HTML multiligne) -->
        <template #detail="{ row }">
          <div v-if="Array.isArray(row.detail)">
            <div v-for="(item, index) in row.detail" :key="index">
              {{ item.quantity }} {{ item.type }}
            </div>
          </div>
          <div v-else>
            {{ row.detail }}
          </div>
        </template>


        <!-- Actions disponibles selon le statut -->
        <template #actions="{ row }">
          <div class="flex gap-3">
            <!-- Bouton "payer" disponible uniquement si en attente -->
            <button
              v-if="row.status === 'en attente'"
              @click="updateStatus(row.id, 'payée')"
              class="text-sm text-green-600 hover:underline"
            >
              💳 Payer
            </button>

            <!-- Bouton "annuler" disponible sauf si déjà annulé -->
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

      <!-- Message si aucune réservation -->
      <p v-else class="mt-4 text-gray-500">
        Aucune réservation pour le moment.
      </p>
    </div>
  </div>
</template>
