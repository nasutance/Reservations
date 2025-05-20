<template>
  <div>
    <h3 class="text-xl font-semibold mb-4">Liste des utilisateurs</h3>

    <!-- Tableau des utilisateurs -->
    <DataTable :headers="headersUser" :fields="fieldsUser" :rows="localUsers">

      <!-- Prénom -->
      <template #firstname="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.firstname" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.firstname }}</span>
      </template>

      <!-- Nom -->
      <template #lastname="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.lastname" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.lastname }}</span>
      </template>

      <!-- Email -->
      <template #email="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.email" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.email }}</span>
      </template>

      <!-- Langue -->
      <template #langue="{ row }">
        <input v-if="isEditingUser(row.id)" v-model="row.langue" class="border px-2 py-1 rounded w-full" />
        <span v-else>{{ row.langue }}</span>
      </template>

      <!-- Rôles (case à cocher multiple en édition) -->
      <template #role="{ row }">
        <div v-if="isEditingUser(row.id)">
          <label v-for="role in roles" :key="role.id" class="block text-sm">
            <input type="checkbox" :value="role.id" v-model="row.selectedRoleIds" class="mr-1" />
            {{ role.role }}
          </label>
        </div>
        <span v-else>{{ row.role }}</span>
      </template>

      <!-- Actions de modification -->
      <template #actions="{ row }">
        <div class="flex flex-col gap-2 items-start mt-1">
          <!-- Bouton de passage en mode édition -->
          <button v-if="!isEditingUser(row.id)" class="text-blue-600 text-sm hover:underline" @click="toggleUserEdit(row.id)">
            ✏️ Modifier
          </button>

          <!-- Boutons en mode édition -->
          <template v-else>
            <button class="text-green-600 text-sm hover:underline" @click="saveUser(row)">💾 Enregistrer</button>
            <button class="text-gray-600 text-sm hover:underline" @click="toggleUserEdit(row.id)">🔄 Annuler</button>
            <button class="text-red-600 text-sm hover:underline" @click="deleteUser(row.id)">🗑️ Supprimer</button>
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
import axios from 'axios'


// Données issues des props Inertia
const page = usePage()
const roles = ref(page.props.roles ?? [])   // Rôles disponibles
const users = ref(page.props.users ?? [])   // Utilisateurs initiaux

// États réactifs
const localUsers = ref([])                  // Données utilisateurs modifiables localement
const editingUserIds = ref(new Set())       // IDs des utilisateurs en édition

// Initialisation + watch sur les props Inertia
hydrateLocalUsers()
watch(() => page.props.users, hydrateLocalUsers)

// Mise en forme locale des utilisateurs
function hydrateLocalUsers() {
  const raw = page.props.users ?? []
  localUsers.value = raw.map(user => ({
    id: user.id,
    firstname: user.firstname,
    lastname: user.lastname,
    email: user.email,
    langue: user.langue,
    selectedRoleIds: user.roles?.map(r => r.id) ?? [],
    role: user.roles?.map(r => r.role).join(', ') || '-',
  }))
}

// Fonctions de contrôle d’édition
function isEditingUser(id) {
  return editingUserIds.value.has(id)
}

function toggleUserEdit(id) {
  isEditingUser(id) ? editingUserIds.value.delete(id) : editingUserIds.value.add(id)
}

// Enregistrer les modifications d’un utilisateur
function saveUser(row) {
  router.put(`/users/${row.id}`, {
    firstname: row.firstname,
    lastname: row.lastname,
    email: row.email,
    langue: row.langue,
    roles: row.selectedRoleIds,
  }, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
    onSuccess: () => {
      window.location.reload()
    },
    onError: (errors) => {
      console.error('Erreur de validation', errors)
    }
  })
}

// Suppression définitive d’un utilisateur
function deleteUser(id) {
  if (!confirm('Supprimer définitivement cet utilisateur ?')) return

  router.delete(`/users/${id}`, {
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
  },
  onSuccess: () => {
    window.location.reload()
  },
  onError: (error) => {
    console.error('Erreur de suppression', error)
  }
})

}

// Définition des colonnes du tableau
const headersUser = ['Prénom', 'Nom', 'Email', 'Langue', 'Rôle', 'Actions']
const fieldsUser = ['firstname', 'lastname', 'email', 'langue', 'role', 'actions']
</script>
