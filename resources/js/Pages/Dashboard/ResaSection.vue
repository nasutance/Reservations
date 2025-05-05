<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des réservations</h3>
    <DataTable :headers="headersResa" :fields="fieldsResa" :rows="formattedReservations">
      <template #status="{ row }">
        <select v-if="isEditingResa(row.id)" v-model="row.status" class="border rounded px-2 py-1">
          <option value="en attente">en attente</option>
          <option value="payée">Payée</option>
          <option value="annulée">Annulée</option>
        </select>
        <span v-else>{{ row.status }}</span>
      </template>

      <template #detail="{ row }">
        <div v-if="isEditingResa(row.id)">
          <!-- Mode édition -->
          <div v-for="(rep, index) in row.representations" :key="index" class="mb-2">
            <label class="block text-xs mb-1">Quantité</label>
            <input type="number" v-model.number="rep.pivot.quantity" class="border rounded px-2 py-1 w-10" min="0" />

            <label class="block text-xs mt-2 mb-1">Tarif</label>
            <select v-model="rep.pivot.price_id" class="border rounded px-2 py-1 w-full">
              <optgroup label="🎟️ Tarifs publics">
              <option
v-for="price in getAvailablePrices(rep).public"
:key="'pub-' + price.id"
:value="price.id"
:disabled="isPriceSelectedElsewhere(row, rep, price.id)"
>
{{ price.description }} — {{ price.price }} €
</option>

              </optgroup>
              <optgroup label="🔒 Tarifs internes">
                <option v-for="price in getAvailablePrices(rep).internal" :key="'int-' + price.id" :value="price.id">
                  {{ price.description }} — {{ price.price }} €
                </option>
              </optgroup>
            </select>
          </div>

          <div class="mt-2 text-sm">
            💰 <strong>Total:</strong> {{ formatMoney(calculateTotal(row)) }}
          </div>

          <div v-if="row.originalTotal !== undefined" class="text-sm"
               :class="{
                 'text-green-600': calculateDifference(row) < 0,
                 'text-red-600': calculateDifference(row) > 0
               }">
            🔁 <strong>Différence:</strong> {{ formatMoney(calculateDifference(row)) }}
          </div>
          </div>

          <div v-else>
            <!-- Mode non-édition -->
            <div v-for="(rep, index) in row.representations" :key="index">
              <!-- Affiche uniquement si la quantité > 0 -->
              <span v-if="rep.pivot.quantity > 0" v-html="rep.pivot.quantity + ' ' + getPriceDescription(rep)" />
            </div>
            <div class="mt-1 text-sm">
              💰 <strong>Total:</strong> {{ formatMoney(row.originalTotal) }}
            </div>
          </div>
        </template>

      <template #actions="{ row }">
        <div class="flex gap-2 items-center">
        <button
          v-if="isEditingResa(row.id)"
          @click="addRepresentationLine(row)"
          class="text-green-600 text-sm ml-2"
        >
          ➕
        </button>


          <!-- Bouton pour Modifier ou Annuler l'édition -->
          <button @click="toggleResaEdit(row.id)" class="text-sm text-blue-600">
            {{ isEditingResa(row.id) ? 'Annuler' : '✏️ Modifier' }}
          </button>

          <!-- Bouton pour Enregistrer les modifications -->
          <button
            v-if="isEditingResa(row.id)"
            @click="saveReservation(row)"
            class="text-sm text-green-600"
          >
            💾 Enregistrer
          </button>

          <!-- Bouton pour Supprimer une réservation -->
          <button
            v-if="isEditingResa(row.id)"
            @click="deleteReservation(row.id)"
            class="text-sm text-red-500 ml-2"
            title="Supprimer la réservation"
          >
            ❌
          </button>

        </div>
      </template>

    </DataTable>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import Button from '@/Components/Button.vue'
import DataTable from '@/Components/DataTable.vue'
import useFormattedReservations from '@/utils/useFormattedReservations'

const { formattedReservations } = useFormattedReservations()
const activeSection = ref('')
const headersResa = ['#', 'Utilisateur', 'Spectacle', 'Date', 'Lieu', 'Statut', 'Détails', 'Actions']
const fieldsResa = ['id', 'user', 'showTitle', 'schedule', 'location', 'status', 'detail', 'actions']

const priceShow = usePage().props.priceShow ?? []
const prices = usePage().props.prices ?? []
const editingResaIds = ref(new Set())

function isEditingResa(id) {
  return editingResaIds.value.has(id)
}

function toggleResaEdit(id) {
  if (isEditingResa(id)) editingResaIds.value.delete(id)
  else editingResaIds.value.add(id)
}

function calculateTotal(row) {
  return row.representations.reduce((total, rep) => {
    const price = prices.find(p => p.id === rep.pivot.price_id)
    return total + (rep.pivot.quantity * (price?.price ?? 0))
  }, 0)
}

function calculateDifference(row) {
  const newTotal = calculateTotal(row)
  const diff = newTotal - (row.originalTotal ?? newTotal)
  return diff
}

function formatMoney(amount) {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

function saveReservation(row) {
  router.patch(`/reservation/${row.id}`, {
    status: row.status,
    representations: row.representations.map(rep => ({
      price_id: rep.pivot.price_id,
      quantity: rep.pivot.quantity,
      original_price_id: rep.pivot.original_price_id,
      original_quantity: rep.pivot.original_quantity
    }))
  }, {
    onSuccess: () => {
      editingResaIds.value.delete(row.id)
      router.reload({ preserveScroll: true })
    }
  })
}


function deleteReservation(id) {
  if (!confirm('Supprimer cette réservation ?')) return;

  router.delete(`/reservation/${id}`, {
    onSuccess: () => {
      // Réinitialiser explicitement l'état d'édition pour cette réservation
      editingResaIds.value.clear();  // Réinitialiser l'état d'édition

      // Forcer un rechargement complet de la page pour quitter le mode édition
      window.location.reload();  // Force un rafraîchissement complet de la page
    }
  })
}

function getAvailablePrices(rep) {
  const showId = rep.show_id

  const publicPrices = priceShow
    .filter(ps => ps.show_id === showId)
    .map(ps => ps.price_id)

  return {
    public: prices.filter(p => publicPrices.includes(p.id)),
    internal: prices.filter(p => !publicPrices.includes(p.id))
  }
}

function getPriceDescription(rep) {
  const price = prices.find(p => p.id === rep.pivot.price_id)
  return price ? price.description : 'Non défini'
}

function isPriceSelectedElsewhere(row, currentRep, priceId) {
  return row.representations.some(
    rep =>
      rep !== currentRep &&
      rep.pivot.price_id === priceId
  )
}

function addRepresentationLine(row) {
  // Récupérer tous les price_id déjà utilisés dans cette réservation
  const usedPriceIds = row.representations.map(rep => rep.pivot.price_id)

  // Trouver le premier tarif non encore utilisé
  const availablePrice = prices.find(p => !usedPriceIds.includes(p.id))
  if (!availablePrice) {
    alert("Aucun tarif supplémentaire disponible à ajouter.")
    return
  }

  // Envoyer la requête POST à Laravel pour insérer la ligne en base
  router.post(`/reservation/${row.id}/add-line`, {
    price_id: availablePrice.id
  }, {
    preserveScroll: true
  })
}


</script>
