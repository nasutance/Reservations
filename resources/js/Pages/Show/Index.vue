<template>
  <AppLayout>
    <Head title="Spectacles" />

    <!-- En-tête -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Nos spectacles</h1>
      <p class="mt-1 text-gray-500">Découvrez notre programmation et réservez votre place.</p>
    </div>

    <!-- Barre de filtres -->
    <form @submit.prevent="filter" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <input
          type="text"
          v-model="filters.q"
          placeholder="Rechercher…"
          class="col-span-2 md:col-span-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 border"
        />
        <input
          type="number"
          v-model="filters.min_duration"
          placeholder="Durée min (min)"
          class="rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 border"
        />
        <input
          type="number"
          v-model="filters.max_duration"
          placeholder="Durée max (min)"
          class="rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 border"
        />
        <input
          type="text"
          v-model="filters.postal_code"
          placeholder="Code postal"
          class="rounded-lg border-gray-300 shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 border"
        />
      </div>

      <div class="flex flex-wrap items-center gap-3 mt-3">
        <select
          v-model="filters.sort"
          class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">-- Trier par --</option>
          <option value="title">Titre</option>
          <option value="duration">Durée</option>
          <option value="created_in">Année</option>
        </select>

        <select
          v-model="filters.direction"
          class="rounded-lg border border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="asc">Croissant</option>
          <option value="desc">Décroissant</option>
        </select>

        <button
          type="submit"
          class="ml-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition"
        >
          Filtrer
        </button>
        <button
          @click.prevent="reset"
          class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition"
        >
          Réinitialiser
        </button>
      </div>
    </form>

    <!-- Grille de cards -->
    <div
      v-if="shows.data.length"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
    >
      <div
        v-for="show in shows.data"
        :key="show.id"
        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow"
      >
        <!-- Poster -->
        <Link :href="route('show.show', show.id)" class="block">
          <div class="relative h-48 bg-gradient-to-br from-indigo-100 to-purple-100 overflow-hidden">
            <img
              v-if="show.poster_url"
              :src="`/images/${show.poster_url}`"
              :alt="show.title"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-16 h-16 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                  d="M15 10l4.553-2.069A1 1 0 0121 8.882v6.236a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" />
              </svg>
            </div>

            <!-- Badge bookable -->
            <span
              v-if="show.bookable"
              class="absolute top-2 right-2 bg-green-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
            >
              Réservable
            </span>
            <span
              v-else
              class="absolute top-2 right-2 bg-gray-400 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
            >
              Indisponible
            </span>
          </div>
        </Link>

        <!-- Corps -->
        <div class="p-4 flex flex-col flex-1">
          <Link
            :href="route('show.show', show.id)"
            class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-2 leading-snug"
          >
            {{ show.title }}
          </Link>

          <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-500">
            <span v-if="show.duration">⏱ {{ show.duration }} min</span>
            <span v-if="show.created_in">📅 {{ show.created_in }}</span>
            <span v-if="show.representations_count === 1">🎭 1 représentation</span>
            <span v-else-if="show.representations_count > 1">🎭 {{ show.representations_count }} représentations</span>
            <span v-else class="italic">Aucune représentation</span>
          </div>

          <!-- Bouton en bas -->
          <div class="mt-auto pt-4">
            <ReserveButton
              :show-id="show.id"
              :bookable="show.bookable"
              :representations-count="show.representations_count"
              class="w-full"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Aucun résultat -->
    <div v-else class="text-center py-20 text-gray-400">
      <svg class="mx-auto w-12 h-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <p class="text-lg font-medium">Aucun spectacle trouvé</p>
      <p class="text-sm mt-1">Essayez de modifier vos filtres.</p>
    </div>

    <div class="mt-8">
      <Pagination :links="shows.links" />
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'
import Pagination from '@/Components/Pagination.vue'
import ReserveButton from '@/Components/ReserveButton.vue'

const page = usePage()
const shows = computed(() => page.props.shows)

const filters = reactive({
  q:            page.props.filters.q            || '',
  min_duration: page.props.filters.min_duration || '',
  max_duration: page.props.filters.max_duration || '',
  postal_code:  page.props.filters.postal_code  || '',
  sort:         page.props.filters.sort         || '',
  direction:    page.props.filters.direction    || 'asc',
})

function filter() {
  router.get(route('show.index'), filters, { preserveScroll: true, preserveState: true })
}

function reset() {
  Object.assign(filters, {
    q: '', min_duration: '', max_duration: '',
    postal_code: '', sort: '', direction: 'asc',
  })
  filter()
}
</script>
