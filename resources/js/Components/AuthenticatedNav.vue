<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

const csrfToken = usePage().props.csrf_token
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


      <!-- Desktop menu -->
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
          <input type="hidden" name="_token" :value="csrfToken">
          <button type="submit" class="text-red-600 hover:underline">
            Déconnexion
          </button>
        </form>

        
              <!-- Bouton RSS -->
              <a href="/rss" target="_blank" title="Flux RSS"
           class="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white font-semibold text-xs px-3 py-2 rounded">
          <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 24 24">
            <path d="M6.18 20.82a2.18 2.18 0 1 1 0-4.36 2.18 2.18 0 0 1 0 4.36zM4 4v3.09a13.91 13.91 0 0 1 13.91 13.91H21A17 17 0 0 0 4 4zm0 5v3.09a8.91 8.91 0 0 1 8.91 8.91H16A11.93 11.93 0 0 0 4 9z"/>
          </svg>
          RSS
        </a>
      </div>

      <!-- Mobile burger -->
      <div class="sm:hidden">
        <button @click="menuOpen = !menuOpen" class="text-gray-600 focus:outline-none">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!menuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-if="menuOpen" class="sm:hidden mt-3 space-y-2 px-2">
      <Link :href="route('show.index')" class="block text-gray-700 hover:text-indigo-600">Spectacles</Link>

      <!-- RSS bouton mobile -->
      <a href="/rss" target="_blank" class="block bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-3 py-2 rounded">
        🔊 RSS
      </a>

      <Link v-if="user" :href="route('dashboard')" class="block text-gray-700 hover:text-indigo-600">Dashboard</Link>
      <Link v-if="user" :href="route('profile.edit')" class="block text-gray-700 hover:text-indigo-600">Profil</Link>
      <Link v-if="!user" :href="route('login')" class="block text-gray-700 hover:text-indigo-600">Connexion</Link>
      <Link v-if="!user" :href="route('register')" class="block text-gray-700 hover:text-indigo-600">Inscription</Link>

      <form v-if="user" method="POST" :action="route('logout')">
        <input type="hidden" name="_token" :value="csrfToken">
        <button type="submit" class="block text-red-600 hover:underline w-full text-left">
          Déconnexion
        </button>
      </form>
    </div>
  </nav>
</template>
