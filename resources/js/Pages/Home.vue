<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  upcoming: {
    type: Array,
    default: () => [],
  },
})

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('fr-BE', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<template>
  <AppLayout>
    <Head title="Accueil" />

    <!-- Hero -->
    <section class="text-center py-16 px-4">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-6">
        <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M16 4v2m0 0V4m0 2a2 2 0 012 2v10a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2m8 0H8m4-4v4" />
        </svg>
      </div>
      <h1 class="text-4xl font-extrabold text-gray-900 mb-4">
        Bienvenue sur le site de réservation
      </h1>
      <p class="text-lg text-gray-500 max-w-xl mx-auto mb-8">
        Découvrez notre programmation, choisissez votre représentation et réservez votre place en quelques clics.
      </p>
      <Link
        :href="route('show.index')"
        class="inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition shadow"
      >
        Voir tous les spectacles →
      </Link>
    </section>

    <!-- Prochaines représentations -->
    <section v-if="upcoming.length" class="mt-4">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Prochaines représentations</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="repr in upcoming"
          :key="repr.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col"
        >
          <!-- Poster -->
          <Link :href="route('show.show', repr.show.id)" class="block">
            <div class="relative h-40 bg-gradient-to-br from-indigo-100 to-purple-100">
              <img
                v-if="repr.show.poster_url"
                :src="`/images/${repr.show.poster_url}`"
                :alt="repr.show.title"
                class="w-full h-full object-cover"
              />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
                </svg>
              </div>
            </div>
          </Link>

          <!-- Infos -->
          <div class="p-4 flex flex-col flex-1">
            <Link
              :href="route('show.show', repr.show.id)"
              class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-1"
            >
              {{ repr.show.title }}
            </Link>

            <p class="mt-1 text-sm text-indigo-600 font-medium">
              📅 {{ formatDate(repr.schedule) }}
            </p>

            <p v-if="repr.location" class="mt-1 text-sm text-gray-500">
              📍 {{ repr.location.designation }}
            </p>
            <p v-else-if="repr.show.location" class="mt-1 text-sm text-gray-500">
              📍 {{ repr.show.location.designation }}
            </p>

            <div class="mt-auto pt-4">
              <Link
                :href="route('show.show', repr.show.id)"
                class="block text-center w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition"
              >
                Réserver
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8 text-center">
        <Link
          :href="route('show.index')"
          class="text-indigo-600 hover:text-indigo-800 font-medium text-sm transition"
        >
          Voir toute la programmation →
        </Link>
      </div>
    </section>

    <!-- Aucune représentation à venir -->
    <section v-else class="mt-4 text-center py-16 text-gray-400">
      <svg class="mx-auto w-10 h-10 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <p class="font-medium">Aucune représentation à venir pour l'instant.</p>
      <Link :href="route('show.index')" class="mt-3 inline-block text-sm text-indigo-600 hover:underline">
        Voir tous les spectacles
      </Link>
    </section>
  </AppLayout>
</template>
