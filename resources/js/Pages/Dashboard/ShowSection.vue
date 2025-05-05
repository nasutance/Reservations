<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des spectacles</h3>

    <DataTable :headers="headersShow" :fields="fieldsShow" :rows="formattedShows">

      <!-- Réservable -->
      <template #bookable="{ row }">
        <div v-if="isEditing[row.id]">
          <select
            v-model="row.bookable"
            class="border px-2 py-1 rounded text-sm"
          >
            <option :value="true">Oui</option>
            <option :value="false">Non</option>
          </select>
        </div>
        <span v-else>{{ row.bookable ? 'Oui' : 'Non' }}</span>
      </template>

      <!-- Tarifs associés -->
      <template #prices="{ row }">
        <div v-if="isEditing[row.id]" class="flex flex-col gap-1">
          <label
            v-for="price in prices"
            :key="price.id"
            class="flex items-center gap-2 text-sm"
          >
            <input
              type="checkbox"
              :value="price.id"
              v-model="assignedPrices[row.id]"
              class="accent-blue-600"
            />
            {{ price.description }} — {{ price.price }}€
          </label>
        </div>
        <ul v-else class="text-sm text-gray-700 list-disc pl-4 mb-1">
          <li
            v-for="price in getPricesForShow(row.id)"
            :key="price.id"
          >
            {{ price.description }} — {{ price.price }}€
          </li>
        </ul>
      </template>

      <!-- Représentations -->
      <template #representations="{ row }">
        <ToggleDetails openLabel="Voir représentations" closeLabel="Masquer représentations">
          <ul class="pl-4 list-disc">
            <li v-for="rep in row.representations" :key="rep.id">
              📅 {{ rep.schedule }} — 📍 {{ rep.location?.designation || '-' }}
            </li>
          </ul>
        </ToggleDetails>
      </template>

      <!-- Actions -->
      <template #actions="{ row }">
        <div class="flex gap-2">
          <button
            v-if="!isEditing[row.id]"
            class="text-blue-600 text-sm hover:underline"
            @click="editRow(row.id)"
          >
            ✏️ Modifier
          </button>

          <div v-else class="flex gap-2">
            <button
              class="bg-green-600 text-white text-sm px-2 py-1 rounded"
              @click="saveRow(row)"
            >
              💾 Enregistrer
            </button>
            <button
              class="bg-gray-300 text-gray-800 text-sm px-2 py-1 rounded"
              @click="cancelEdit(row.id)"
            >
              ❌ Annuler
            </button>
          </div>
        </div>
      </template>

    </DataTable>
  </div>
</template>

<script setup>
import useFormattedShows from '@/utils/useFormattedShows'
import { ref } from 'vue'
import DataTable from '@/Components/DataTable.vue'
import ToggleDetails from '@/Components/ToggleDetails.vue'
import { usePage, router } from '@inertiajs/vue3'

const { formattedShows } = useFormattedShows()

const headersShow = ['Titre', 'Description', 'Durée', 'Réservable', 'Tarifs', 'Représentations', 'Actions']
const fieldsShow = ['title', 'description', 'duration', 'bookable', 'prices', 'representations', 'actions']

const prices = usePage().props.prices ?? []
const priceShow = usePage().props.priceShow ?? []

const assignedPrices = ref({})
const isEditing = ref({})

// Initialisation des tarifs associés à chaque show
formattedShows.value.forEach(show => {
  assignedPrices.value[show.id] = priceShow
    .filter(ps => ps.show_id === show.id)
    .map(ps => ps.price_id)
})

function getPricesForShow(showId) {
  const showPriceIds = priceShow.filter(p => p.show_id === showId).map(p => p.price_id)
  return prices.filter(p => showPriceIds.includes(p.id))
}

function editRow(id) {
  isEditing.value[id] = true
  if (!assignedPrices.value[id]) {
    assignedPrices.value[id] = []
  }
}

function cancelEdit(id) {
  isEditing.value[id] = false
}

function saveRow(row) {
  router.put(`/show/${row.id}`, {
    bookable: row.bookable,
    price_ids: assignedPrices.value[row.id]
  }, {
    onSuccess: () => {
      isEditing.value[row.id] = false
      window.location.reload()
    }
  })
}
</script>
