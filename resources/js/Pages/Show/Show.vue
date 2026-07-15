<template>
<AppLayout>
  <article class="max-w-5xl mx-auto">

    <!-- En-tête : affiche + informations principales -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="md:flex">

        <!-- Affiche -->
        <div class="md:w-72 md:shrink-0 relative h-64 md:h-auto bg-gradient-to-br from-indigo-100 to-purple-100">
          <img
            v-if="show.poster_url"
            :src="`/images/${show.poster_url}`"
            :alt="show.title"
            class="w-full h-full object-cover"
            @error="show.poster_url = null"
          />
          <div v-else class="w-full h-full flex items-center justify-center">
            <svg class="w-16 h-16 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
            </svg>
          </div>
        </div>

        <!-- Infos -->
        <div class="p-6 flex flex-col flex-1">
          <div class="flex items-start justify-between gap-4">
            <h1 class="text-3xl font-bold text-gray-900">{{ show.title }}</h1>
            <span
              :class="show.bookable ? 'bg-green-500' : 'bg-gray-400'"
              class="shrink-0 text-white text-xs font-semibold px-3 py-1 rounded-full"
            >
              {{ show.bookable ? 'Réservable' : 'Indisponible' }}
            </span>
          </div>

          <p v-if="show.description" class="mt-4 text-gray-600 whitespace-pre-line">{{ show.description }}</p>

          <dl class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div v-if="show.location">
              <dt class="font-semibold text-gray-500">Lieu de création</dt>
              <dd class="mt-0.5 text-gray-900">{{ show.location.designation }}</dd>
            </div>
            <div v-if="show.duration">
              <dt class="font-semibold text-gray-500">Durée</dt>
              <dd class="mt-0.5 text-gray-900">{{ show.duration }} minutes</dd>
            </div>
            <div v-if="show.created_in">
              <dt class="font-semibold text-gray-500">Année de création</dt>
              <dd class="mt-0.5 text-gray-900">{{ show.created_in }}</dd>
            </div>
          </dl>
        </div>
      </div>
    </div>

    <!-- Représentations -->
    <section class="mt-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Représentations à venir</h2>

      <ul v-if="show.representations.length" class="space-y-3">
        <li
          v-for="representation in show.representations"
          :key="representation.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:flex items-center justify-between gap-4"
        >
          <div>
            <p class="font-medium text-indigo-600">
              📅 {{ formatDate(representation.schedule) }}
            </p>
            <p class="mt-0.5 text-sm text-gray-500">
              📍
              <template v-if="representation.location">{{ representation.location.designation }}</template>
              <template v-else-if="show.location">{{ show.location.designation }}</template>
              <template v-else>Lieu à déterminer</template>
            </p>
          </div>
          <div class="mt-3 sm:mt-0 shrink-0">
            <ReserveButton
              :show-id="show.id"
              :bookable="show.bookable"
              :representations-count="show.representations.length"
            />
          </div>
        </li>
      </ul>
      <p v-else class="text-gray-500">Aucune représentation programmée pour l’instant.</p>
    </section>

    <!-- Distribution -->
    <section v-if="hasCollaborateurs" class="mt-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Distribution</h2>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
        <div v-if="collaborateurs.auteur.length">
          <dt class="font-semibold text-gray-500">Auteur</dt>
          <dd class="mt-0.5 text-gray-900">{{ formatNames(collaborateurs.auteur) }}</dd>
        </div>
        <div v-if="collaborateurs['scénographe'].length">
          <dt class="font-semibold text-gray-500">Mise en scène</dt>
          <dd class="mt-0.5 text-gray-900">{{ formatNames(collaborateurs['scénographe']) }}</dd>
        </div>
        <div v-if="collaborateurs['comédien'].length">
          <dt class="font-semibold text-gray-500">Interprétation</dt>
          <dd class="mt-0.5 text-gray-900">{{ formatNames(collaborateurs['comédien']) }}</dd>
        </div>
      </div>
    </section>

    <nav class="mt-8">
      <Link :href="route('show.index')" class="text-indigo-600 hover:text-indigo-800 font-medium">
        ← Retour au catalogue
      </Link>
    </nav>
  </article>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage, Link } from '@inertiajs/vue3'
import ReserveButton from '@/Components/ReserveButton.vue'
import { formatDate } from '@/utils/formatDate'
import { computed } from 'vue'

const page = usePage()
const show = page.props.show

// Regroupe les artistes par fonction (auteur, scénographe, comédien)
const collaborateurs = computed(() => {
  const mapping = {
    auteur: [],
    scénographe: [],
    comédien: [],
  }

  for (const at of show.artist_types ?? []) {
    const type = at.type?.type
    if (mapping[type]) {
      mapping[type].push(at.artist)
    }
  }

  return mapping
})

const hasCollaborateurs = computed(() =>
  Object.values(collaborateurs.value).some((list) => list.length > 0)
)

// « Prénom Nom, Prénom Nom et Prénom Nom »
function formatNames(artists) {
  const names = artists.map((a) => `${a.firstname} ${a.lastname}`)
  if (names.length <= 1) return names.join('')

  return names.slice(0, -1).join(', ') + ' et ' + names.at(-1)
}
</script>
