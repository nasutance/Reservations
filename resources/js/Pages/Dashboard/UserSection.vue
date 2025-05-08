<script setup>
import CrudSection from '@/Components/CrudSection.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const roles = computed(() => page.props.roles)

const columns = [
  { key: 'firstname', label: 'Prénom', editable: true },
  { key: 'lastname', label: 'Nom', editable: true },
  { key: 'email', label: 'Email', editable: true },
  { key: 'langue', label: 'Langue', editable: true },
  {
    key: 'role',
    label: 'Rôle',
    editable: true,
    type: 'checkboxes',
    options: roles.value.map(r => ({ id: r.id, label: r.role })),
    bindKey: 'selectedRoleIds'
  }
]

const defaultItem = {
  firstname: '',
  lastname: '',
  email: '',
  langue: '',
  selectedRoleIds: [],
  role: '-'
}

function formatItem(user) {
  return {
    id: user.id,
    firstname: user.firstname,
    lastname: user.lastname,
    email: user.email,
    langue: user.langue,
    selectedRoleIds: user.roles?.map(r => r.id) ?? [],
    role: user.roles?.map(r => r.role).join(', ') || '-',
  }
}
</script>

<template>
  <CrudSection
    title="Liste des utilisateurs"
    :columns="columns"
    resource="users"
    :defaultItem="defaultItem"
    :formatItem="formatItem"
  />
</template>
