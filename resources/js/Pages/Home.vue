<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <AppLayout>
    <Head title="Accueil" />

    <div class="min-h-screen flex flex-col items-center justify-center text-center px-6 py-20">
      <h1 class="text-4xl font-bold text-gray-800 mb-4">
        Bienvenue sur le site de réservation 🎭
      </h1>
      <p class="text-lg text-gray-700 mb-6 max-w-2xl">
        Découvre les spectacles et connecte-toi pour réserver ta place.
      </p>
    </div>
  </AppLayout>
</template>
