<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des utilisateurs</h3>
    <DataTable :headers="headersUser" :fields="fieldsUser" :rows="formattedUsers">
      <template #firstname="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.firstname" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.firstname }}</span>
      </template>

      <template #lastname="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.lastname" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.lastname }}</span>
      </template>

      <template #email="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.email" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.email }}</span>
      </template>

      <template #langue="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.langue" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.langue }}</span>
      </template>

      <template #role="{ row }">
        <div v-if="isEditingUser(row.id)">
          <label v-for="role in roles" :key="role.id" class="block text-sm">
            <input type="checkbox" :value="role.id" v-model="row.selectedRoleIds" class="mr-1" />
            {{ role.role }}
          </label>
        </div>
        <span v-else>{{ row.role }}</span>
      </template>

      <template #actions="{ row }">
        <div class="flex gap-2 items-center">
          <button @click="toggleUserEdit(row.id)" class="text-sm text-blue-600">
            {{ isEditingUser(row.id) ? 'Annuler' : '✏️ Modifier' }}
          </button>
          <button v-if="isEditingUser(row.id)" @click="saveUser(row)" class="text-sm text-green-600">
            💾 Enregistrer
          </button>
          <button
            v-if="isEditingUser(row.id)"
            @click="deleteUser(row.id)"
            class="text-sm text-red-500 ml-2"
            title="Supprimer l’utilisateur"
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

import DataTable from '@/Components/DataTable.vue'
import useFormattedUsers from '@/utils/useFormattedUsers'

const roles = usePage().props.roles ?? []
const { formattedUsers } = useFormattedUsers()

const headersUser = ['Prénom', 'Nom', 'Email', 'Langue', 'Rôle', 'Actions']
const fieldsUser = ['firstname', 'lastname', 'email', 'langue', 'role', 'actions']

const editingUserIds = ref(new Set())

function isEditingUser(id) {
  return editingUserIds.value.has(id)
}

function toggleUserEdit(id) {
  if (isEditingUser(id)) editingUserIds.value.delete(id)
  else editingUserIds.value.add(id)
}

function saveUser(row) {
  router.put(`/users/${row.id}`, {
    firstname: row.firstname,
    lastname: row.lastname,
    email: row.email,
    langue: row.langue,
    roles: row.selectedRoleIds,
  }, {
    onSuccess: () => {
      editingUserIds.value.delete(row.id)
      router.reload({ preserveScroll: true })
    }
  })
}

function deleteUser(id) {
  if (!confirm('Supprimer définitivement cet utilisateur ?')) return

  router.delete(`/users/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      editingUserIds.value.delete(id)
      router.reload({ preserveScroll: true })
    }
  })
}
</script>
