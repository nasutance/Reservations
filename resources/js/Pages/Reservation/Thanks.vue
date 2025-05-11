<template>
  <!-- Page de remerciement affichée après paiement réussi -->
  <AppLayout>
    <div class="max-w-xl mx-auto py-20 text-center">
      <h1 class="text-3xl font-bold text-green-600 mb-4">🎉 Merci pour votre réservation !</h1>

      <!-- Résumé de confirmation incluant le numéro de réservation -->
      <p class="text-lg mb-2">
        Votre réservation <strong>#{{ reservationId }}</strong> a bien été enregistrée et payée.
      </p>
      <p class="text-gray-700">
        Un email de confirmation vous a été envoyé.<br>
        Vous pouvez retrouver cette réservation dans votre tableau de bord.
      </p>

      <!-- Bouton de redirection vers le tableau de bord utilisateur -->
      <button class="mt-6 btn" @click="goDashboard">Aller au tableau de bord</button>
    </div>
  </AppLayout>
</template>

<script setup>
// Importation du layout principal et des outils Inertia
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Récupération de l'identifiant de la réservation depuis les props de la page Inertia
const reservationId = usePage().props.reservationId

// Redirige l'utilisateur vers son tableau de bord
function goDashboard() {
  router.visit('/dashboard')
}
</script>

<style scoped>
/* Style du bouton d'action */
.btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
