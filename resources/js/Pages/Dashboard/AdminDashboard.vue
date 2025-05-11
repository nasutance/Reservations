<template>
  <div>
    <!-- Titre principal de la page admin -->
    <h2 class="text-2xl font-bold mb-6">Espace Admin</h2>

    <!-- Barre de navigation pour choisir la section à afficher -->
    <div class="flex space-x-4 mb-8">
      <!-- Chaque bouton modifie la valeur de activeSection -->
      <Button @click="activeSection = 'users'">Utilisateurs</Button>
      <Button @click="activeSection = 'reservations'">Réservations</Button>
      <Button @click="activeSection = 'shows'">Spectacles</Button>
      <Button @click="activeSection = 'representations'">Représentations</Button>
      <Button @click="activeSection = 'prices'">Prix</Button>
      <Button @click="activeSection = 'artists'">Artistes</Button>
    </div>

    <div>
      <!-- Affichage conditionnel de chaque section selon la valeur de activeSection -->

      <!-- Section des utilisateurs (ex : liste, rôles, suppression, etc.) -->
      <UserSection v-if="activeSection === 'users'" />

      <!-- Section des réservations (par date, user, représentation, etc.) -->
      <ResaSection v-if="activeSection === 'reservations'" />

      <!-- Section des spectacles (affiches, titres, auteurs, etc.) -->
      <ShowSection v-if="activeSection === 'shows'" />

      <!-- Section des représentations (lieu, date, heure, etc.) -->
      <RepresentationSection v-if="activeSection === 'representations'" />

      <!-- Section des tarifs appliqués aux spectacles -->
      <PriceSection v-if="activeSection === 'prices'" />

      <!-- Section des artistes (prénom, nom, type, etc.) -->
      <ArtistSection v-if="activeSection === 'artists'" />
    </div>
  </div>
</template>

<script setup>
// Importation de la fonction ref pour créer une variable réactive
import { ref } from 'vue'

// Composant bouton personnalisé (probablement stylisé avec Tailwind ou autre)
import Button from '@/Components/Button.vue'

// Importation des différentes sections d'administration
import ArtistSection from '@/Pages/Dashboard/ArtistSection.vue'
import UserSection from '@/Pages/Dashboard/UserSection.vue'
import ResaSection from '@/Pages/Dashboard/ResaSection.vue'
import PriceSection from '@/Pages/Dashboard/PriceSection.vue'
import ShowSection from '@/Pages/Dashboard/ShowSection.vue'
import RepresentationSection from '@/Pages/Dashboard/RepresentationSection.vue'

// Variable réactive pour suivre la section actuellement active
const activeSection = ref('')
</script>
