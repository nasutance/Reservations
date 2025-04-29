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
      <!-- Section Utilisateurs -->
      <div v-if="activeSection === 'users'">
        <h3 class="text-xl font-semibold mb-4">Liste des utilisateurs</h3>
        <DataTable :headers="headersUser" :fields="fieldsUser" :rows="formattedUsers" />
      </div>

      <!-- Section Réservations -->
      <div v-if="activeSection === 'reservations'">
        <h3 class="text-xl font-semibold mb-4">Liste des réservations</h3>
        <DataTable :headers="headersResa" :fields="fieldsResa" :rows="formattedReservations" />
      </div>

      <!-- Section Spectacles -->
      <div v-if="activeSection === 'shows'">
        <h3 class="text-xl font-semibold mb-4">Liste des spectacles</h3>

        <DataTable :headers="headersShow" :fields="fieldsShow" :rows="formattedShows">
          <!-- Slot personnalisé pour les représentations -->
          <template #representations="{ row }">
            <ToggleDetails openLabel="Voir représentations" closeLabel="Masquer représentations">
              <ul class="pl-4 list-disc">
                <li v-for="rep in row.representations" :key="rep.id">
                  📅 {{ rep.schedule }} — 📍 {{ rep.location?.designation || '-' }}
                </li>
              </ul>
            </ToggleDetails>
          </template>
        </DataTable>
      </div>

      <!-- Section Artistes -->
      <div v-if="activeSection === 'artists'">
        <h3 class="text-xl font-semibold mb-4">Liste des artistes</h3>
        <DataTable :headers="headersArtists" :fields="fieldsArtists" :rows="formattedArtists" />
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Button from '@/Components/Button.vue';
import DataTable from '@/Components/DataTable.vue';
import ToggleDetails from '@/Components/ToggleDetails.vue';

import useFormattedReservations from '@/utils/useFormattedReservations';
import useFormattedUsers from '@/utils/useFormattedUsers';
import useFormattedShows from '@/utils/useFormattedShows';
import useFormattedArtists from '@/utils/useFormattedArtists';

const { formattedReservations } = useFormattedReservations();
const { formattedUsers } = useFormattedUsers();

const { formattedShows } = useFormattedShows();
const { formattedArtists } = useFormattedArtists();

const activeSection = ref('');

// Headers pour Admin Réservations
const headersResa = ['#', 'Utilisateur', 'Spectacle', 'Représentation', 'Statut', 'Détails'];
const fieldsResa = ['id', 'user', 'showTitle', 'schedule', 'status', 'detail'];

// Headers pour Admin Utilisateurs
const headersUser = ['#', 'Prénom', 'Nom', 'Email', 'Langue', 'Rôle'];
const fieldsUser = ['id', 'firstname', 'lastname', 'email', 'langue', 'role'];

// Headers pour Admin Spectacles
const headersShow = ['#', 'Titre', 'Description', 'Durée', 'Réservable', 'Représentations'];
const fieldsShow = ['id', 'title', 'description', 'duration', 'bookable', 'representations'];

// Headers pour Admin Artistes
const headersArtists = ['Artiste', 'Types', 'Spectacles'];
const fieldsArtists = ['fullname', 'typesText', 'showsText'];


</script>
