<script setup>
import { ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { formatDate } from '@/utils/formatDate.js'
import DataTable from '@/Components/DataTable.vue'

// Accès aux données injectées via Inertia (props)
const page = usePage()
const locations = ref(page.props.locations ?? [])           // Lieux disponibles
const shows = ref(page.props.shows ?? [])                   // Spectacles disponibles
const representations = ref(page.props.representations ?? []) // Représentations du backend

// Données locales modifiables
const localRepresentations = ref([])                        // Copie modifiable des représentations
const editingIds = ref(new Set())                           // IDs des lignes en cours d’édition

// Hydrate les données locales au chargement initial
hydrateLocalRepresentations()

// Met à jour localRepresentations si props.representations change
watch(() => page.props.representations, hydrateLocalRepresentations)

// Copie chaque représentation pour usage local, évite mutation directe
function hydrateLocalRepresentations() {
  const reps = page.props.representations ?? []
  localRepresentations.value = reps.map(rep => ({
    ...rep,
    location_id: rep.location_id,
    show_id: rep.show_id,
  }))
}

// Vérifie si une ligne est en cours d'édition
function isEditing(id) {
  return editingIds.value.has(id)
}

// Alterne le mode édition pour une ligne
function toggleEdit(id) {
  if (isEditing(id)) editingIds.value.delete(id)
  else editingIds.value.add(id)
}

// Ajoute une ligne vide en haut du tableau
function addNewRepresentationRow() {
  const newRow = {
    id: `new-${Date.now()}`, // ID temporaire pour usage local
    schedule: '',
    location_id: locations.value[0]?.id ?? null,
    show_id: shows.value[0]?.id ?? null,
    isNew: true
  }
  localRepresentations.value.unshift(newRow)
  editingIds.value.add(newRow.id)
}

// Enregistre la ligne (création ou mise à jour)
function save(row) {
  const method = row.isNew ? 'post' : 'put'
  const url = row.isNew ? '/representation' : `/representation/${row.id}`

  router[method](url, {
    schedule: row.schedule,
    location_id: row.location_id,
    show_id: row.show_id
  }, {
    onSuccess: () => {
      editingIds.value.delete(row.id)
      router.reload({ preserveScroll: true }) // Recharge les données après maj
    }
  })
}

// Supprime une représentation après confirmation
function deleteRow(id) {
  if (!confirm('Supprimer définitivement cette représentation ?')) return

  router.delete(`/representation/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingIds.value.delete(id)
      router.reload({ preserveScroll: true })
    }
  })
}

// Configuration des colonnes du tableau
const headers = ['Date & Heure', 'Lieu', 'Spectacle', 'Actions']
const fields = ['schedule', 'location', 'show', 'actions']
</script>

<template>
  <div>
    <!-- Titre de la section -->
    <h3 class="text-xl font-semibold mb-4">Liste des représentations</h3>

    <!-- Bouton pour ajouter une nouvelle ligne -->
    <button @click="addNewRepresentationRow">➕ Ajouter une représentation</button>

    <!-- Composant tableau réutilisable -->
    <DataTable :headers="headers" :fields="fields" :rows="localRepresentations">

      <!-- Date & heure -->
      <template #schedule="{ row }">
        <input
          v-if="isEditing(row.id)"
          type="datetime-local"
          v-model="row.schedule"
          class="border px-2 py-1 rounded w-full"
        />
        <span v-else>{{ formatDate(row.schedule, { time: true }) }}</span>
      </template>

      <!-- Sélecteur de lieu -->
      <template #location="{ row }">
        <select v-if="isEditing(row.id)" v-model="row.location_id" class="border px-2 py-1 rounded w-full">
          <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.designation }}</option>
        </select>
        <span v-else>{{ row.location?.designation ?? '—' }}</span>
      </template>

      <!-- Sélecteur de spectacle -->
      <template #show="{ row }">
        <select v-if="isEditing(row.id)" v-model="row.show_id" class="border px-2 py-1 rounded w-full">
          <option v-for="s in shows" :key="s.id" :value="s.id">{{ s.title }}</option>
        </select>
        <span v-else>{{ row.show?.title ?? '—' }}</span>
      </template>

      <!-- Actions : modifier, enregistrer, supprimer -->
      <template #actions="{ row }">
        <div class="flex gap-2 items-center">
          <button @click="toggleEdit(row.id)" class="text-sm text-blue-600">
            {{ isEditing(row.id) ? 'Annuler' : '✏️ Modifier' }}
          </button>
          <button v-if="isEditing(row.id)" @click="save(row)" class="text-sm text-green-600">
            💾 Enregistrer
          </button>
          <button
            v-if="isEditing(row.id) && !row.isNew"
            @click="deleteRow(row.id)"
            class="text-sm text-red-500 ml-2"
          >
            ❌
          </button>
        </div>
      </template>

    </DataTable>
  </div>
</template>
