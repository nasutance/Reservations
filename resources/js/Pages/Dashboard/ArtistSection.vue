<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des artistes</h3>
    <Button @click="openCreateModal('artist')">Créer artiste</Button>

    <DataTable :headers="headersArtists" :fields="fieldsArtists" :rows="formattedArtists">
      <!-- Champ editable : fullname -->
      <template #fullname="{ row }">
        <div v-if="isEditing(row.id)">
          <input v-model="row.firstname" placeholder="Prénom" class="border px-2 py-1 rounded mr-1" />
          <input v-model="row.lastname" placeholder="Nom" class="border px-2 py-1 rounded" />
        </div>
        <span v-else>{{ row.fullname }}</span>
      </template>

      <!-- Champ editable : types -->
      <template #typesText="{ row }">
        <div v-if="isEditing(row.id)">
          <label v-for="type in types" :key="type.id" class="block text-sm">
            <input type="checkbox" :value="type.id" v-model="row.selectedTypeIds" class="mr-1" />
            {{ type.type }}
          </label>
        </div>
        <span v-else>{{ row.typesText }}</span>
      </template>

      <!-- Champ editable : shows par type -->
      <template #showsText="{ row }">
        <div v-if="isEditing(row.id)">
          <div v-for="typeId in row.selectedTypeIds" :key="typeId" class="mb-2">
            <strong class="text-sm block">{{ getTypeLabel(typeId) }}</strong>
            <select v-model="row.selectedShowTypeMap[typeId]" multiple class="w-full border rounded px-2 py-1">
              <option v-for="show in shows" :key="show.id" :value="show.id">
                {{ show.title }}
              </option>
            </select>
          </div>
        </div>
        <span v-else>{{ row.showsText }}</span>
      </template>

      <!-- Actions -->
      <template #actions="{ row }">
        <div class="flex gap-2 items-center">
          <button @click="toggleEdit(row.id)" class="text-sm text-blue-600">
            {{ isEditing(row.id) ? 'Annuler' : '✏️ Modifier' }}
          </button>

          <button
            v-if="isEditing(row.id)"
            @click="saveArtist(row)"
            class="text-sm text-green-600"
          >
            💾 Enregistrer
          </button>

          <button
            v-if="isEditing(row.id)"
            @click="deleteArtist(row.id)"
            class="text-sm text-red-500 ml-2"
            title="Supprimer l'artiste"
          >
            ❌
          </button>
        </div>
      </template>
    </DataTable>

    <!-- Modal création -->
    <CreateModal
      v-if="showModal"
      :entity="selectedEntity"
      :fields="fields"
      :submit-url="submitUrl"
      @close="closeModal"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import Button from '@/Components/Button.vue'
import DataTable from '@/Components/DataTable.vue'
import CreateModal from '@/Components/CreateModal.vue'
import { formSchemas } from '@/utils/formSchemas'
import useFormattedArtists from '@/utils/useFormattedArtists'

const { formattedArtists } = useFormattedArtists()

const types = usePage().props.types ?? []
const shows = usePage().props.shows ?? []

function getTypeLabel(id) {
  return types.find(t => t.id === id)?.type ?? `Type ${id}`
}

const headersArtists = ['Artiste', 'Types', 'Spectacles', 'Actions']
const fieldsArtists = ['fullname', 'typesText', 'showsText', 'actions']

const showModal = ref(false)
const selectedEntity = ref(null)
const fields = ref([])
const submitUrl = ref('')

function openCreateModal(entity) {
  selectedEntity.value = entity
  fields.value = formSchemas[entity]
  submitUrl.value = entity === 'artist' ? '/artist' : ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  router.reload({ preserveScroll: true })
}

const editingArtistIds = ref(new Set())

function isEditing(id) {
  return editingArtistIds.value.has(id)
}

function toggleEdit(id) {
  if (isEditing(id)) editingArtistIds.value.delete(id)
  else editingArtistIds.value.add(id)
}

async function saveArtist(row) {
  await router.put(`/artist/${row.id}`, {
    firstname: row.firstname,
    lastname: row.lastname,
    types: row.selectedTypeIds,
    shows: row.selectedShowTypeMap,
  }, {
    onSuccess: () => {
      editingArtistIds.value.delete(row.id)
      router.reload({ preserveScroll: true })
    }
  })
}

function deleteArtist(id) {
  if (!confirm('Supprimer définitivement cet artiste ?')) return

  router.delete(`/artist/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingArtistIds.value.delete(id)
      router.reload({ preserveScroll: true })
    }
  })
}
</script>
