<template>
<AppLayout>
  <div class="max-w-xl mx-auto py-20 text-center">
    <h1 class="text-3xl font-bold text-green-600 mb-4">🎉 Merci pour votre réservation !</h1>

    <p class="text-lg mb-2">
      Votre réservation <strong>#{{ reservationId }}</strong> a bien été enregistrée et payée.
    </p>
    <p class="text-gray-700">
      Un email de confirmation vous a été envoyé.<br>
      Vous pouvez retrouver cette réservation dans votre tableau de bord.
    </p>

    <button class="mt-6 btn" @click="goDashboard">Aller au tableau de bord</button>
  </div>
  </AppLayout>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
const reservationId = usePage().props.reservationId

function goDashboard() {
  router.visit('/dashboard')
}
</script>

<style scoped>
.btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
