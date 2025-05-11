<template>
  <!-- Définition du titre de la page (balise <title>) -->
  <Head title="Mon espace" />

  <!-- Layout principal avec en-tête intégré -->
  <AppLayout>
    <!-- Slot nommé "header" pour injecter un titre dans la mise en page -->
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Mon espace</h2>
    </template>

    <!-- Contenu principal de la page -->
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Affiche le tableau de bord admin si l'utilisateur a le rôle admin -->
        <AdminDashboard v-if="isAdmin" />

        <!-- Sinon, affiche le tableau de bord du membre -->
        <MemberDashboard v-else />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminDashboard from '@/Pages/Dashboard/AdminDashboard.vue'
import MemberDashboard from '@/Pages/Dashboard/MemberDashboard.vue'

// Récupération de l'utilisateur connecté via Inertia
const user = usePage().props.auth?.user || {}

// Vérifie si l'utilisateur possède le rôle 'admin'
const isAdmin = user?.roles?.some(role => role.role === 'admin')
</script>
