<template>
  <nav class="bg-white shadow px-4 py-3 flex justify-between items-center">
  <a href="/" class="text-lg font-bold text-indigo-700">
    🎟️ Réservations
  </a>


    <div class="space-x-4">
      <Link :href="route('show.index')" class="text-gray-700 hover:text-indigo-600">
        Spectacles
      </Link>
      <Link v-if="user" :href="route('dashboard')" class="text-gray-700 hover:text-indigo-600">
        Dashboard
      </Link>
      <Link v-if="!user" :href="route('login')" class="text-gray-700 hover:text-indigo-600">
        Connexion
      </Link>
      <Link v-if="!user" :href="route('register')" class="text-gray-700 hover:text-indigo-600">
        Inscription
      </Link>
      <Link v-if="user" :href="route('logout')" method="post" class="text-red-600 hover:underline">
        Déconnexion
      </Link>
    </div>
  </nav>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const user = usePage().props.auth?.user ?? null
</script>
