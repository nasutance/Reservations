<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Espace Admin</h2>

    <div class="flex space-x-4 mb-8">
      <Button @click="activeSection = 'users'">Utilisateurs</Button>
      <Button @click="activeSection = 'reservations'">Réservations</Button>
      <Button @click="activeSection = 'shows'">Spectacles</Button>
      <Button @click="activeSection = 'artists'">Artistes</Button>
    </div>

    <div>
      <div  v-if="activeSection === 'users'">
        <h3 class="text-xl font-semibold mb-4">Liste des utilisateurs</h3>
        <DataTable :headers="headersUser" :fields="fieldsUser" :rows="formattedUsers" />
      </div>

      <div v-if="activeSection === 'reservations'">
        <h3 class="text-xl font-semibold mb-4">Liste des réservations</h3>
        <DataTable :headers="headersResa" :fields="fieldsResa" :rows="formattedReservations" />
      </div>

      <div v-if="activeSection === 'shows'">
        <h3 class="text-xl font-semibold mb-4">Liste des spectacles</h3>
        <DataTable :headers="headersShow" :fields="fieldsShow" :rows="formattedShows" />
      </div>

      <p v-if="activeSection === 'artists'">Liste des artistes ici</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Button from '@/Components/Button.vue'
import DataTable from '@/Components/DataTable.vue'
import { formatDate } from '@/utils'
import useFormattedReservations from '@/utils/useFormattedReservations';
import useFormattedUsers from '@/utils/useFormattedUsers';
import useFormattedShows from '@/utils/useFormattedShows';

const { formattedReservations } = useFormattedReservations();
const { formattedUsers } = useFormattedUsers();
const { formattedShows } = useFormattedShows();
const activeSection = ref('')

// Headers pour Admin Réservations
const headersResa = ['#', 'Utilisateur', 'Spectacle', 'Représentation', 'Statut', 'Détails']
const fieldsResa = ['id', 'user', 'showTitle', 'schedule', 'status', 'detail']

// Headers pour Admin Utilisateurs
const headersUser = ['#', 'Prénom', 'Nom', 'email', 'Langue', 'Rôle']
const fieldsUser = ['id', 'firstname', 'lastname', 'email', 'langue', 'role']

// Headers pour Admin Spectacles
const headersShow = ['#', 'Titre', 'Description', 'Durée', 'Lieu', 'Réservable', 'Représentations']
const fieldsShow = ['id', 'title', 'description', 'duration', 'location', 'bookable', 'representations']

</script>
