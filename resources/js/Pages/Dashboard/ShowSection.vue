<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des spectacles</h3>
    <div class="flex gap-4 mb-4">
      <button @click="exportCSV" class="text-sm text-blue-600 hover:underline">📤Exporter en CSV</button>
      <input type="file" ref="csvFile" @change="importCSV" accept=".csv" class="hidden" />
      <button @click="csvFile.click()" class="text-sm text-blue-600 hover:underline">📥Importer un CSV</button>
    </div>

    <DataTable :headers="headersShow" :fields="fieldsShow" :rows="localShows">
      <template #title="{ row }">
        <div v-if="isEditing[row.id]">
          <input v-model="row.title" class="border px-2 py-1 rounded text-sm w-full" />
        </div>
        <span v-else>{{ row.title }}</span>
      </template>

      <template #description="{ row }">
        <div v-if="isEditing[row.id]">
          <textarea v-model="row.description" class="border px-2 py-1 rounded text-sm w-full" rows="3" />
        </div>
        <span v-else>{{ row.description }}</span>
      </template>

      <template #duration="{ row }">
        <div v-if="isEditing[row.id]">
          <input type="number" v-model="row.duration" min="1" class="border px-2 py-1 rounded text-sm w-24" />
        </div>
        <span v-else>{{ row.duration }} '</span>
      </template>

      <template #bookable="{ row }">
        <div class="flex justify-center items-center h-full">
          <template v-if="isEditing[row.id]">
            <select v-model="row.bookable" class="border px-2 py-1 rounded text-sm">
              <option :value="1">Oui</option>
              <option :value="0">Non</option>
            </select>
          </template>
          <span v-else>{{ row.bookable ? 'Oui' : 'Non' }}</span>
        </div>
      </template>

      <template #prices="{ row }">
        <div v-if="isEditing[row.id]" class="flex flex-col gap-2">
          <label v-for="price in prices" :key="price.id" class="flex items-center gap-2 text-sm">
            <input
              type="checkbox"
              :value="price.id"
              v-model="assignedPrices[row.id]"
              class="accent-blue-600"
            />
            {{ price.description }} {{ price.price }}€
          </label>
        </div>
        <ul v-else class="text-sm text-gray-700 list-disc pl-4 mb-1">
          <li v-for="price in getPricesForShow(row.id)" :key="price.id">
            {{ price.description }} {{ price.price }}€
          </li>
        </ul>
      </template>

      <template #representations="{ row }">
        <div class="flex justify-center items-center h-full">
          <ToggleDetails openLabel="Détails" closeLabel="Masquer">
            <ul class="text-sm text-gray-700 list-disc pl-4 mb-1">
              <li v-for="rep in row.representations" :key="rep.id">
                {{ formatDate(rep.schedule, { time: true, seconds: false }) }}<br />
                {{ rep.location?.designation || '-' }}
              </li>
            </ul>
          </ToggleDetails>
        </div>
      </template>

      <template #actions="{ row }">
        <div class="flex flex-col gap-2 items-start mt-1">
          <button v-if="!isEditing[row.id]" class="text-blue-600 text-sm hover:underline" @click="editRow(row.id)">
            ✏️ Modifier
          </button>
          <template v-else>
            <button class="text-green-600 text-sm hover:underline" @click="saveRow(row)">
              ✅Enregistrer
            </button>
            <button class="text-gray-600 text-sm hover:underline" @click="cancelEdit(row.id)">
              🔄Annuler
            </button>
            <button class="text-red-600 text-sm hover:underline" @click="deleteShow(row.id)">
              🗑️Supprimer
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
import ToggleDetails from '@/Components/ToggleDetails.vue'
import { formatDate } from '@/utils/formatDate.js'

// Props Inertia
const page = usePage()
const shows = ref(page.props.shows ?? [])
const representations = ref(page.props.representations ?? [])
const prices = ref(page.props.prices ?? [])
const priceShow = ref(page.props.priceShow ?? [])

// État local
const localShows = ref([])
const assignedPrices = ref({})
const isEditing = ref({})

// Reconstruction initiale
hydrateLocalShows()
hydrateAssignedPrices()

// Watch sur mise à jour via Inertia
watch(() => page.props.shows, hydrateLocalShows)
watch(() => page.props.priceShow, hydrateAssignedPrices)

// Fonctions de reconstruction
function hydrateLocalShows() {
  const raw = page.props.shows ?? []
  const reps = page.props.representations ?? []

  localShows.value = raw.map(show => ({
    ...show,
    duration: show.duration ?? '',
    bookable: Boolean(show.bookable),
    representations: reps.filter(r => r.show_id === show.id),
  }))
}

function hydrateAssignedPrices() {
  const mapping = {}
  const current = page.props.priceShow ?? []

  localShows.value.forEach(show => {
    mapping[show.id] = current
      .filter(ps => ps.show_id === show.id)
      .map(ps => ps.price_id)
  })

  priceShow.value = [...current]
  assignedPrices.value = mapping
}

// Fonctions de rendu
function getPricesForShow(showId) {
  const ids = priceShow.value.filter(p => p.show_id === showId).map(p => p.price_id)
  return prices.value.filter(p => ids.includes(p.id))
}

// Actions
function editRow(id) {
  isEditing.value[id] = true
  if (!assignedPrices.value[id]) assignedPrices.value[id] = []
}

function cancelEdit(id) {
  isEditing.value[id] = false
}

function saveRow(row) {
  router.put(`/show/${row.id}`, {
    title: row.title,
    description: row.description,
    duration: row.duration,
    bookable: Number(row.bookable),
    price_ids: assignedPrices.value[row.id],
  }, {
    onSuccess: () => {
      isEditing.value[row.id] = false
      router.reload({ preserveScroll: true })
    }
  })
}

function deleteShow(id) {
  if (!confirm('Supprimer définitivement ce spectacle ?')) return
  router.delete(`/show/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      isEditing.value[id] = false
      router.reload({ preserveScroll: true })
    }
  })
}

function exportCSV() {
  window.open(route('shows.export'), '_blank')
}

function importCSV(event) {
  const file = event.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('csv_file', file)
  router.post(route('shows.import'), formData, {
    forceFormData: true,
    onSuccess: () => router.reload()
  })
}

const csvFile = ref(null)
const headersShow = ['Titre', 'Description', 'Durée', 'Réservable', 'Tarification', 'Représentations', 'Actions']
const fieldsShow = ['title', 'description', 'duration', 'bookable', 'prices', 'representations', 'actions']
</script>
