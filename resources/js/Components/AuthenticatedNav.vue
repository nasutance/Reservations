<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const user = usePage().props.auth?.user ?? null
const menuOpen = ref(false)
</script>

<template>
  <nav class="bg-white shadow px-4 py-3">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <!-- Logo -->
      <Link href="/" class="text-lg font-bold text-indigo-700 flex items-center gap-1">
        🎟️ Réservations
      </Link>

      <!-- Desktop Links -->
      <div class="hidden sm:flex items-center space-x-4 text-sm">
        <Link :href="route('show.index')" class="text-gray-700 hover:text-indigo-600">
          Spectacles
        </Link>

        <Link v-if="user" :href="route('dashboard')" class="text-gray-700 hover:text-indigo-600">
          Mon espace
        </Link>

        <Link v-if="user" :href="route('profile.edit')" class="text-gray-700 hover:text-indigo-600">
          Profil
        </Link>

        <Link v-if="!user" :href="route('login')" class="text-gray-700 hover:text-indigo-600">
          Connexion
        </Link>

        <Link v-if="!user" :href="route('register')" class="text-gray-700 hover:text-indigo-600">
          Inscription
        </Link>

        <form v-if="user" method="POST" :action="route('logout')" class="inline">
          <button type="submit" class="text-red-600 hover:underline">
            Déconnexion
          </button>
        </form>
      </div>

      <!-- Mobile Hamburger -->
      <div class="sm:hidden">
        <button @click="menuOpen = !menuOpen" class="text-gray-600 focus:outline-none">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!menuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div v-if="menuOpen" class="sm:hidden mt-3 space-y-2 px-2">
      <Link :href="route('show.index')" class="block text-gray-700 hover:text-indigo-600">Spectacles</Link>
      <Link v-if="user" :href="route('dashboard')" class="block text-gray-700 hover:text-indigo-600">Dashboard</Link>
      <Link v-if="user" :href="route('profile.edit')" class="block text-gray-700 hover:text-indigo-600">Profil</Link>
      <Link v-if="!user" :href="route('login')" class="block text-gray-700 hover:text-indigo-600">Connexion</Link>
      <Link v-if="!user" :href="route('register')" class="block text-gray-700 hover:text-indigo-600">Inscription</Link>
      <form v-if="user" method="POST" :action="route('logout')">
        <button type="submit" class="block text-red-600 hover:underline w-full text-left">Déconnexion</button>
      </form>
    </div>
  </nav>
</template>
