<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des prix</h3>
    <button @click="addNewPriceRow">➕ Ajouter un prix</button>

    <DataTable :headers="headersPrice" :fields="fieldsPrice" :rows="localPrices">
      <template #type="{ row }">
        <input v-if="isEditingPrice(row.id)" v-model="row.type" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.type }}</span>
      </template>

      <template #description="{ row }">
        <input v-if="isEditingPrice(row.id)" v-model="row.description" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.description }}</span>
      </template>

      <template #price="{ row }">
        <input
          v-if="isEditingPrice(row.id)"
          type="number"
          v-model="row.price"
          min="0"
          step="0.01"
          class="border px-2 py-1 rounded w-20"
        />
        <span v-else>{{ row.price }}€</span>
      </template>

      <template #start_date="{ row }">
        <input
          v-if="isEditingPrice(row.id)"
          type="date"
          v-model="row.start_date"
          class="border px-2 py-1 rounded w-full"
        />
        <span v-else>{{ formatDate(row.start_date, { time: false }) }}</span>
      </template>

      <template #end_date="{ row }">
        <input
          v-if="isEditingPrice(row.id)"
          type="date"
          v-model="row.end_date"
          class="border px-2 py-1 rounded w-full"
        />
        <span v-else>{{ formatDate(row.end_date, { time: false }) }}</span>
      </template>

      <template #actions="{ row }">
  <div class="flex flex-col gap-2 items-start mt-1">
    <button
      v-if="!isEditingPrice(row.id)"
      class="text-blue-600 text-sm hover:underline"
      @click="togglePriceEdit(row.id)"
    >
      ✏️ Modifier
    </button>

    <template v-else>
      <button
        class="text-green-600 text-sm hover:underline"
        @click="savePrice(row)"
      >
        💾 Enregistrer
      </button>
      <button
        class="text-gray-600 text-sm hover:underline"
        @click="togglePriceEdit(row.id)"
      >
        🔄 Annuler
      </button>
      <button
        v-if="!row.isNew"
        class="text-red-600 text-sm hover:underline"
        @click="deletePrice(row.id)"
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
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { formatDate } from '@/utils/formatDate.js'
import DataTable from '@/Components/DataTable.vue'

// Réf locale, modifiable
const initialPrices = usePage().props.prices ?? []
const localPrices = ref([...initialPrices])

const headersPrice = ['Libellé', 'Description', 'Montant', 'Début', 'Fin', 'Actions']
const fieldsPrice = ['type', 'description', 'price', 'start_date', 'end_date', 'actions']
const editingPriceIds = ref(new Set())

function isEditingPrice(id) {
  return editingPriceIds.value.has(id)
}

function addNewPriceRow() {
  const newRow = {
    id: `new-${Date.now()}`,
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

function togglePriceEdit(id) {
  if (isEditingPrice(id)) editingPriceIds.value.delete(id)
  else editingPriceIds.value.add(id)
}

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
    onSuccess: () => {
      editingPriceIds.value.delete(row.id)
      router.reload({ preserveScroll: true })
    }
  })
}

function deletePrice(id) {
  if (!confirm('Supprimer définitivement ce prix ?')) return

  router.delete(`/price/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingPriceIds.value.delete(id)
      router.reload({ preserveScroll: true })
    }
  })
}
</script>
