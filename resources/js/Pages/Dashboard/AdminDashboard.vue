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
      <UserSection v-if="activeSection === 'users'" />

      <!-- Section Réservations -->
      <ResaSection v-if="activeSection === 'reservations'" />


      <!-- Section Spectacles -->
      <div v-if="activeSection === 'shows'">
        <h3 class="text-xl font-semibold mb-4">Liste des spectacles</h3>
        <DataTable :headers="headersShow" :fields="fieldsShow" :rows="formattedShows">
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
      <ArtistSection v-if="activeSection === 'artists'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import Button from '@/Components/Button.vue'
import DataTable from '@/Components/DataTable.vue'
import ToggleDetails from '@/Components/ToggleDetails.vue'
import ArtistSection from '@/Pages/Dashboard/ArtistSection.vue'
import UserSection from '@/Pages/Dashboard/UserSection.vue'
import ResaSection from '@/Pages/Dashboard/ResaSection.vue'

import useFormattedShows from '@/utils/useFormattedShows'

const { formattedShows } = useFormattedShows()

const activeSection = ref('')

const headersShow = ['#', 'Titre', 'Description', 'Durée', 'Réservable', 'Représentations']
const fieldsShow = ['id', 'title', 'description', 'duration', 'bookable', 'representations']

</script>
