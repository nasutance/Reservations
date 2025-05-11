<template>
  <div>
    <!-- Titre de la section -->
    <h3 class="text-xl font-semibold mb-4">Liste des prix</h3>

    <!-- Bouton pour ajouter un nouveau prix -->
    <button @click="addNewPriceRow">➕ Ajouter un prix</button>

    <!-- Composant DataTable avec colonnes dynamiques et slots -->
    <DataTable :headers="headersPrice" :fields="fieldsPrice" :rows="localPrices">

      <!-- Édition ou affichage simple du champ "type" -->
      <template #type="{ row }">
        <input v-if="isEditingPrice(row.id)" v-model="row.type" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.type }}</span>
      </template>

      <!-- Description -->
      <template #description="{ row }">
        <input v-if="isEditingPrice(row.id)" v-model="row.description" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.description }}</span>
      </template>

      <!-- Montant -->
      <template #price="{ row }">
        <input v-if="isEditingPrice(row.id)" type="number" v-model="row.price" min="0" step="0.01" class="border px-2 py-1 rounded w-20" />
        <span v-else>{{ row.price }}€</span>
      </template>

      <!-- Date de début de validité -->
      <template #start_date="{ row }">
        <input v-if="isEditingPrice(row.id)" type="date" v-model="row.start_date" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ formatDate(row.start_date, { time: false }) }}</span>
      </template>

      <!-- Date de fin de validité -->
      <template #end_date="{ row }">
        <input v-if="isEditingPrice(row.id)" type="date" v-model="row.end_date" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ formatDate(row.end_date, { time: false }) }}</span>
      </template>

      <!-- Actions disponibles selon l'état de la ligne -->
      <template #actions="{ row }">
        <div class="flex flex-col gap-2 items-start mt-1">
          <!-- Bouton "Modifier" -->
          <button v-if="!isEditingPrice(row.id)" class="text-blue-600 text-sm hover:underline" @click="togglePriceEdit(row.id)">
            ✏️ Modifier
          </button>

          <!-- Boutons "Enregistrer", "Annuler" et "Supprimer" -->
          <template v-else>
            <button class="text-green-600 text-sm hover:underline" @click="savePrice(row)">💾 Enregistrer</button>
            <button class="text-gray-600 text-sm hover:underline" @click="togglePriceEdit(row.id)">🔄 Annuler</button>
            <button v-if="!row.isNew" class="text-red-600 text-sm hover:underline" @click="deletePrice(row.id)">🗑️ Supprimer</button>
          </template>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { formatDate } from '@/utils/formatDate.js'
import DataTable from '@/Components/DataTable.vue'

// Initialisation : récupère les prix passés par Inertia
const initialPrices = usePage().props.prices ?? []

// Copie locale modifiable des prix
const localPrices = ref([...initialPrices])

// Ensemble des IDs en mode édition
const editingPriceIds = ref(new Set())

// Définition des colonnes du tableau
const headersPrice = ['Libellé', 'Description', 'Montant', 'Début', 'Fin', 'Actions']
const fieldsPrice = ['type', 'description', 'price', 'start_date', 'end_date', 'actions']

// Watcher : met à jour localPrices à chaque changement côté serveur
watch(() => usePage().props.prices, (newPrices) => {
  localPrices.value = [...newPrices]
})

// Vérifie si un prix est actuellement en édition
function isEditingPrice(id) {
  return editingPriceIds.value.has(id)
}

// Ajoute une ligne vide pour créer un nouveau prix
function addNewPriceRow() {
  const newRow = {
    id: `new-${Date.now()}`, // ID temporaire
    type: '',
    description: '',
    price: 0,
    start_date: '',
    end_date: '',
    isNew: true
  }
  localPrices.value.unshift(newRow)
  editingPriceIds.value.add(newRow.id)
}

// Active/désactive l'édition pour un prix donné
function togglePriceEdit(id) {
  if (isEditingPrice(id)) editingPriceIds.value.delete(id)
  else editingPriceIds.value.add(id)
}

// Enregistre un prix (création ou mise à jour selon le cas)
function savePrice(row) {
  const method = row.isNew ? 'post' : 'put'
  const url = row.isNew ? '/price' : `/price/${row.id}`

  router[method](url, {
    type: row.type,
    description: row.description,
    price: row.price,
    start_date: row.start_date,
    end_date: row.end_date
  }, {
    preserveScroll: true,
    onSuccess: () => {
      editingPriceIds.value.clear() // Quitte le mode édition
      router.reload({ only: ['prices'], preserveScroll: true }) // Recharge uniquement les prix
    }
  })
}

// Supprime un prix avec confirmation
function deletePrice(id) {
  if (!confirm('Supprimer définitivement ce prix ?')) return

  router.delete(`/price/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingPriceIds.value.clear()
      router.reload({ only: ['prices'], preserveScroll: true })
    }
  })
}
</script>
