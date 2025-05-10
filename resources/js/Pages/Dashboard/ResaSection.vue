<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des réservations</h3>

    <DataTable :headers="headersResa" :fields="fieldsResa" :rows="localReservations">
      <template #status="{ row }">
        <select v-if="isEditingResa(row.id)" v-model="row.status" class="border rounded px-2 py-1">
          <option value="en attente">En attente</option>
          <option value="payée">Payée</option>
          <option value="annulée">Annulée</option>
        </select>
        <span v-else>{{ row.status }}</span>
      </template>

      <template #detail="{ row }">
        <div v-if="isEditingResa(row.id)">
          <div class="flex gap-2 mb-1 text-xs font-semibold text-gray-500">
            <div class="w-[60px]">Qté</div>
            <div class="flex-1">Tarif</div>
            <div class="w-6"></div>
          </div>

          <div
            v-for="(rep, index) in row.representations"
            :key="index"
            class="flex items-center gap-2 mb-2"
          >
            <input
              type="number"
              v-model.number="rep.pivot.quantity"
              min="0"
              class="border rounded px-2 py-1 w-[60px] text-sm"
            />

            <select
              v-model="rep.pivot.price_id"
              class="border rounded px-2 py-1 flex-1 text-sm"
            >
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
                <option
                  v-for="price in getAvailablePrices(rep).internal"
                  :key="'int-' + price.id"
                  :value="price.id"
                >
                  {{ price.description }} — {{ price.price }} €
                </option>
              </optgroup>
            </select>
            <button
  @click="deleteRepresentationLine(row.id, rep.id, rep.pivot.price_id)"
  class="text-red-600 text-sm"
  title="Supprimer cette ligne"
>
  ❌
</button>

          </div>

          <div class="mt-2 text-sm">
            💰 <strong>Total:</strong> {{ formatMoney(calculateTotal(row)) }}
          </div>
          <div
            v-if="row.originalTotal !== undefined"
            class="text-sm"
            :class="{
              'text-green-600': calculateDifference(row) < 0,
              'text-red-600': calculateDifference(row) > 0
            }"
          >
            🔁 <strong>Différence:</strong> {{ formatMoney(calculateDifference(row)) }}
          </div>
        </div>

        <div v-else>
          <div v-for="(rep, index) in row.representations" :key="index">
            <span v-if="rep.pivot.quantity > 0" v-html="rep.pivot.quantity + ' ' + getPriceDescription(rep)" />
          </div>
          <div class="mt-1 text-sm">
            💰 <strong>Total:</strong> {{ formatMoney(row.originalTotal) }}
          </div>
        </div>
      </template>

      <template #actions="{ row }">
  <div class="flex flex-col gap-2 items-start mt-1">
    <button
      v-if="!isEditingResa(row.id)"
      class="text-blue-600 text-sm hover:underline"
      @click="toggleResaEdit(row.id)"
    >
      ✏️ Modifier
    </button>

    <template v-else>
      <button
        class="text-green-600 text-sm hover:underline"
        @click="saveReservation(row)"
      >
        💾 Enregistrer
      </button>
      <button
        class="text-gray-600 text-sm hover:underline"
        @click="toggleResaEdit(row.id)"
      >
        🔄 Annuler
      </button>
      <button
        class="text-red-600 text-sm hover:underline"
        @click="deleteReservation(row.id)"
      >
        🗑️ Supprimer
      </button>
      <button
        class="text-green-600 text-sm hover:underline"
        @click="addRepresentationLine(row)"
      >
        ➕ Ajouter un tarif
      </button>
    </template>
  </div>
</template>

    </DataTable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DataTable from '@/Components/DataTable.vue'
import { formatDate } from '@/utils/formatDate'

const page = usePage()
const prices = ref(page.props.prices ?? [])
const priceShow = ref(page.props.priceShow ?? [])
const reservations = ref(page.props.reservations ?? [])
const localReservations = ref([])
const editingResaIds = ref(new Set())

hydrateLocalReservations()
watch(() => page.props.reservations, hydrateLocalReservations)

function hydrateLocalReservations() {
  const raw = page.props.reservations ?? []

  localReservations.value = raw.map(resa => {
    const reps = resa.representations.map(rep => ({
      ...rep,
      pivot: {
        ...rep.pivot,
        id: rep.pivot.id, // important pour le bouton supprimer
        original_price_id: rep.pivot.price_id,
        original_quantity: rep.pivot.quantity
      }
    }))

    const total = reps.reduce((sum, rep) => {
      const price = prices.value.find(p => p.id === rep.pivot.price_id)
      return sum + (rep.pivot.quantity * (price?.price ?? 0))
    }, 0)

    return {
      id: resa.id,
      user: resa.user ? `${resa.user.firstname} ${resa.user.lastname}` : '-',
      showTitle: reps[0]?.show?.title || '-',
      schedule: reps[0]?.schedule ? formatDate(reps[0].schedule, true) : '-',
      location: reps[0]?.location?.designation || '-',
      status: resa.status,
      originalTotal: total,
      representations: reps
    }
  })
}

function isEditingResa(id) {
  return editingResaIds.value.has(id)
}

function toggleResaEdit(id) {
  isEditingResa(id) ? editingResaIds.value.delete(id) : editingResaIds.value.add(id)
}

function calculateTotal(row) {
  return row.representations.reduce((total, rep) => {
    const price = prices.value.find(p => p.id === rep.pivot.price_id)
    return total + (rep.pivot.quantity * (price?.price ?? 0))
  }, 0)
}

function calculateDifference(row) {
  const newTotal = calculateTotal(row)
  const original = row.originalTotal ?? newTotal
  return newTotal - original
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
  if (!confirm('Supprimer cette réservation ?')) return
  router.delete(`/reservation/${id}`, {
    onSuccess: () => {
      editingResaIds.value.clear()
      router.reload()
    }
  })
}

function getAvailablePrices(rep) {
  const publicIds = priceShow.value
    .filter(p => p.show_id === rep.show_id)
    .map(p => p.price_id)

  return {
    public: prices.value.filter(p => publicIds.includes(p.id)),
    internal: prices.value.filter(p => !publicIds.includes(p.id))
  }
}

function getPriceDescription(rep) {
  const price = prices.value.find(p => p.id === rep.pivot.price_id)
  return price ? price.description : 'Non défini'
}

function isPriceSelectedElsewhere(row, currentRep, priceId) {
  return row.representations.some(
    rep => rep !== currentRep && rep.pivot.price_id === priceId
  )
}

function addRepresentationLine(row) {
  const usedIds = row.representations.map(r => r.pivot.price_id)
  const available = prices.value.find(p => !usedIds.includes(p.id))
  if (!available) return alert('Aucun tarif supplémentaire disponible à ajouter.')

  router.post(`/reservation/${row.id}/add-line`, {
    price_id: available.id
  }, {
    preserveScroll: true,
    onSuccess: () => {
      // Simuler ajout d'une nouvelle ligne
      row.representations.push({
        id: Date.now(), // valeur temporaire, unique
        show_id: row.representations[0]?.show_id ?? null,
        pivot: {
          id: Date.now(), // identifiant factice temporaire
          quantity: 0,
          price_id: available.id,
          original_price_id: available.id,
          original_quantity: 0
        }
      })
    }
  })
}

function deleteRepresentationLine(resaId, representationId, priceId) {
  if (!confirm('Supprimer cette ligne de réservation ?')) return

  router.delete(`/reservation/${resaId}/line/${representationId}/${priceId}`, {
    preserveScroll: true,
    onSuccess: () => {
      const resa = localReservations.value.find(r => r.id === resaId)
      if (!resa) return

      resa.representations = resa.representations.filter(
        rep => !(rep.id === representationId && rep.pivot.price_id === priceId)
      )
    }
  })
}


const headersResa = ['#', 'Utilisateur', 'Spectacle', 'Date', 'Lieu', 'Statut', 'Détails', 'Actions']
const fieldsResa = ['id', 'user', 'showTitle', 'schedule', 'location', 'status', 'detail', 'actions']
</script>
