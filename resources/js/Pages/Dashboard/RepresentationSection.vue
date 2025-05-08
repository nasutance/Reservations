<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des représentations</h3>
    <button @click="addNewRepresentationRow">➕ Ajouter une représentation</button>

    <DataTable :headers="headers" :fields="fields" :rows="localRepresentations">
      <template #schedule="{ row }">
        <input
          v-if="isEditing(row.id)"
          type="datetime-local"
          v-model="row.schedule"
          class="border px-2 py-1 rounded w-full"
        />
        <span v-else>{{ formatDate(row.schedule, { time: true }) }}</span>
      </template>

      <template #location="{ row }">
        <select v-if="isEditing(row.id)" v-model="row.location_id" class="border px-2 py-1 rounded w-full">
          <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.designation }}</option>
        </select>
        <span v-else>{{ row.location?.designation ?? '—' }}</span>
      </template>

      <template #show="{ row }">
        <select v-if="isEditing(row.id)" v-model="row.show_id" class="border px-2 py-1 rounded w-full">
          <option v-for="s in shows" :key="s.id" :value="s.id">{{ s.title }}</option>
        </select>
        <span v-else>{{ row.show?.title ?? '—' }}</span>
      </template>
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
        @click="save(row)"
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
        @click="deleteRow(row.id)"
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
import { ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { formatDate } from '@/utils/formatDate.js'
import DataTable from '@/Components/DataTable.vue'

const page = usePage()
const locations = ref(page.props.locations ?? [])
const shows = ref(page.props.shows ?? [])
const representations = ref(page.props.representations ?? [])

const localRepresentations = ref([])
const editingIds = ref(new Set())

hydrateLocalRepresentations()

watch(() => page.props.representations, hydrateLocalRepresentations)

function hydrateLocalRepresentations() {
  const reps = page.props.representations ?? []
  localRepresentations.value = reps.map(rep => ({
    ...rep,
    location_id: rep.location_id,
    show_id: rep.show_id,
  }))
}

function isEditing(id) {
  return editingIds.value.has(id)
}

function toggleEdit(id) {
  if (isEditing(id)) editingIds.value.delete(id)
  else editingIds.value.add(id)
}

function addNewRepresentationRow() {
  const newRow = {
    id: `new-${Date.now()}`,
    schedule: '',
    location_id: locations.value[0]?.id ?? null,
    show_id: shows.value[0]?.id ?? null,
    isNew: true
  }
  localRepresentations.value.unshift(newRow)
  editingIds.value.add(newRow.id)
}

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
      router.reload({ preserveScroll: true })
    }
  })
}

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

const headers = ['Date & Heure', 'Lieu', 'Spectacle', 'Actions']
const fields = ['schedule', 'location', 'show', 'actions']
</script>


