<template>
  <div>
    <!-- Titre de la section -->
    <h3 class="text-xl font-semibold mb-4">Liste des artistes</h3>

    <!-- Bouton d'ajout d'un nouvel artiste -->
    <button @click="addNewArtistRow">➕ Ajouter un artiste</button>

    <!-- Composant tableau générique avec slots personnalisés -->
    <DataTable :headers="headers" :fields="fields" :rows="localArtists">

      <!-- Colonne "Artiste" : prénom + nom ou formulaire si édition -->
      <template #fullname="{ row }">
        <div v-if="isEditing(row.id)">
          <input v-model="row.firstname" placeholder="Prénom" class="border px-2 py-1 rounded mr-1" />
          <input v-model="row.lastname" placeholder="Nom" class="border px-2 py-1 rounded" />
        </div>
        <span v-else>{{ row.firstname }} {{ row.lastname }}</span>
      </template>

      <!-- Colonne "Types" : case à cocher si édition, sinon liste -->
      <template #types="{ row }">
        <div v-if="isEditing(row.id)">
          <label v-for="type in types" :key="type.id" class="block text-sm">
            <input type="checkbox" :value="type.id" v-model="row.selectedTypeIds" class="mr-1" />
            {{ type.type }}
          </label>
        </div>
        <span v-else>
          {{ getTypeLabels(row.selectedTypeIds).join(', ') || '—' }}
        </span>
      </template>

      <!-- Colonne "Spectacles" : affichage par type d'artiste si édition -->
      <template #shows="{ row }">
  <div v-if="isEditing(row.id)">
    <div v-for="typeId in row.selectedTypeIds" :key="typeId" class="mb-2">
      <strong class="text-sm block mb-1">{{ getTypeLabel(typeId) }}</strong>
      <div class="grid grid-cols-2 gap-1">
        <label
          v-for="show in shows"
          :key="`type-${typeId}-show-${show.id}`"
          class="text-sm"
        >
          <input
            type="checkbox"
            :value="show.id"
            :checked="row.selectedShowTypeMap[typeId]?.includes(show.id)"
            @change="toggleShowSelection(row, typeId, show.id, $event)"
            class="mr-1"
          />
          {{ show.title }}
        </label>
      </div>
    </div>
  </div>
  <span v-else>
    <span v-if="row.showsText">{{ row.showsText }}</span>
    <span v-else>—</span>
  </span>
</template>


      <!-- Colonne "Actions" : bouton modifier/enregistrer/annuler/supprimer -->
      <template #actions="{ row }">
        <div class="flex flex-col gap-2 items-start mt-1">
          <button
            v-if="!isEditing(row.id)"
            class="text-blue-600 text-sm hover:underline"
            @click="toggleEdit(row.id)"
          >
            ✏️ Modifier
          </button>
          <template v-else>
            <button
              class="text-green-600 text-sm hover:underline"
              @click="saveArtist(row)"
            >
              💾 Enregistrer
            </button>
            <button
              class="text-gray-600 text-sm hover:underline"
              @click="toggleEdit(row.id)"
            >
              🔄 Annuler
            </button>
            <button
              v-if="!row.isNew"
              class="text-red-600 text-sm hover:underline"
              @click="deleteArtist(row.id)"
            >
              🗑️ Supprimer
            </button>
          </template>
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import DataTable from '@/Components/DataTable.vue'

// Récupération des props envoyées par Inertia
const page = usePage()
const types = ref(page.props.types ?? [])           // Liste des types d'artistes
const shows = ref(page.props.shows ?? [])           // Liste des spectacles
const artistTypes = ref(page.props.artistTypes ?? []) // Données pivot artist-type-show

// Données locales pour gérer les artistes côté client
const localArtists = ref([])

// Ensemble des ID d'artistes en cours d'édition
const editingIds = ref(new Set())

// Synchro des données côté client dès que la props change
watch(() => page.props.artists, hydrateLocalArtists)
watch(() => page.props.artistTypes, hydrateLocalArtists)
hydrateLocalArtists()

// Fonction de peuplement de localArtists à partir des props
function hydrateLocalArtists() {
  const rawArtists = page.props.artists ?? []
  const rawArtistTypes = page.props.artistTypes ?? []

  localArtists.value = rawArtists.map(artist => {
    const related = rawArtistTypes.filter(at => at.artist_id === artist.id)
    const typeIds = related.map(at => at.type_id)
    const showMap = Object.fromEntries(
      related.map(at => [
        at.type_id,
        at.shows?.map(s => s.id) ?? []
      ])
    )
    // Construction d’un texte récapitulatif des spectacles liés à chaque type
    const showsTextMap = {}
    for (const at of related) {
      const type = at.type?.type
      for (const show of at.shows ?? []) {
        if (!showsTextMap[show.title]) showsTextMap[show.title] = []
        showsTextMap[show.title].push(type)
      }
    }
    const showsText = Object.entries(showsTextMap)
      .map(([title, tps]) => `${title} (${tps.join(', ')})`)
      .join(' / ')

    return {
      id: artist.id,
      firstname: artist.firstname,
      lastname: artist.lastname,
      selectedTypeIds: typeIds,
      selectedShowTypeMap: showMap,
      showsText
    }
  })
}

// Retourne true si l'artiste est en édition
function isEditing(id) {
  return editingIds.value.has(id)
}

// Alterne le mode édition d’un artiste
function toggleEdit(id) {
  if (isEditing(id)) editingIds.value.delete(id)
  else editingIds.value.add(id)
}

// Ajoute une ligne vide pour insérer un nouvel artiste
function addNewArtistRow() {
  const newRow = {
    id: `new-${Date.now()}`, // ID temporaire local
    firstname: '',
    lastname: '',
    selectedTypeIds: [],
    selectedShowTypeMap: {},
    isNew: true
  }
  localArtists.value.unshift(newRow)
  editingIds.value.add(newRow.id)
}

// Affiche un libellé de type à partir de son ID
function getTypeLabel(id) {
  return types.value.find(t => t.id === id)?.type ?? `Type ${id}`
}

// Retourne un tableau de labels de type depuis une liste d’ID
function getTypeLabels(ids) {
  return ids.map(getTypeLabel)
}

// Récupère un binding pour la relation type → spectacles
function getShowBinding(row, typeId) {
  return computed({
    get() {
      if (!Array.isArray(row.selectedShowTypeMap[typeId])) {
        row.selectedShowTypeMap[typeId] = []
      }
      return row.selectedShowTypeMap[typeId]
    },
    set(newValue) {
      row.selectedShowTypeMap[typeId] = newValue
    }
  })
}

// Enregistre un artiste (création ou mise à jour)
async function saveArtist(row) {
  const method = row.isNew ? 'post' : 'put'
  const url = row.isNew ? '/artist' : `/artist/${row.id}`

  // S’assure que chaque type est bien mappé à un tableau
  for (const typeId of row.selectedTypeIds) {
    if (!Array.isArray(row.selectedShowTypeMap[typeId])) {
      row.selectedShowTypeMap[typeId] = []
    }
  }

  // 🧠 🔧 Correction : cast explicite des clés de type_id en Number
  row.selectedShowTypeMap = Object.fromEntries(
    Object.entries(row.selectedShowTypeMap).map(([k, v]) => [Number(k), v])
  )

  await router[method](url, {
    firstname: row.firstname,
    lastname: row.lastname,
    types: row.selectedTypeIds,
    shows: row.selectedShowTypeMap,
  }, {
    onSuccess: () => {
      editingIds.value.delete(row.id)
      router.reload({ preserveScroll: true })
    }
  })
}


// Supprime un artiste après confirmation
function deleteArtist(id) {
  if (!confirm('Supprimer définitivement cet artiste ?')) return
  router.delete(`/artist/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingIds.value.delete(id)
      router.reload({ preserveScroll: true })
    }
  })
}
function toggleShowSelection(row, typeId, showId, event) {
  if (!Array.isArray(row.selectedShowTypeMap[typeId])) {
    row.selectedShowTypeMap[typeId] = []
  }

  const selected = row.selectedShowTypeMap[typeId]

  if (event.target.checked && !selected.includes(showId)) {
    selected.push(showId)
  } else if (!event.target.checked && selected.includes(showId)) {
    row.selectedShowTypeMap[typeId] = selected.filter(id => id !== showId)
  }
}

// Colonnes du tableau (affichage & correspondance des champs)
const headers = ['Artiste', 'Types', 'Spectacles', 'Actions']
const fields = ['fullname', 'types', 'shows', 'actions']
</script>
