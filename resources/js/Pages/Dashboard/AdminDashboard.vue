<template>
  <div>
    <h2 class="text-2xl font-bold mb-6">Espace Admin</h2>

    <div class="flex space-x-4 mb-8">
      <Button @click="activeSection = 'users'">Utilisateurs</Button>
      <Button @click="activeSection = 'reservations'">Réservations</Button>
      <Button @click="activeSection = 'shows'">Spectacles</Button>
      <Button @click="activeSection = 'representations'">Représentations</Button>
      <Button @click="activeSection = 'prices'">Prix</Button>
      <Button @click="activeSection = 'artists'">Artistes</Button>
    </div>

    <div>
      <!-- Section Utilisateurs -->
      <UserSection v-if="activeSection === 'users'" />

      <!-- Section Réservations -->
      <ResaSection v-if="activeSection === 'reservations'" />

      <!-- Section Spectacles -->
      <ShowSection v-if="activeSection === 'shows'" />

      <!-- Section Spectacles -->
      <RepresentationSection v-if="activeSection === 'representations'" />

      <!-- Section Prix -->
      <PriceSection v-if="activeSection === 'prices'" />

      <!-- Section Artistes -->
      <ArtistSection v-if="activeSection === 'artists'" />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

import Button from '@/Components/Button.vue'
import ArtistSection from '@/Pages/Dashboard/ArtistSection.vue'
import UserSection from '@/Pages/Dashboard/UserSection.vue'
import ResaSection from '@/Pages/Dashboard/ResaSection.vue'
import PriceSection from '@/Pages/Dashboard/PriceSection.vue'
import ShowSection from '@/Pages/Dashboard/ShowSection.vue'
import RepresentationSection from '@/Pages/Dashboard/RepresentationSection.vue'

const activeSection = ref('')

</script>
