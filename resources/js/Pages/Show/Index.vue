<template>
<AppLayout>
  <div>
    <h1 class="text-2xl font-bold mb-4">Liste des spectacles</h1>

    <form @submit.prevent="filter" class="space-y-2 mb-6">
      <input type="text" v-model="filters.q" placeholder="Mot-clé" class="input" />
      <input type="number" v-model="filters.min_duration" placeholder="Durée min" class="input" />
      <input type="number" v-model="filters.max_duration" placeholder="Durée max" class="input" />
      <input type="text" v-model="filters.postal_code" placeholder="Code postal" class="input" />

      <select v-model="filters.sort" class="input">
        <option value="">-- Tri --</option>
        <option value="title">Titre</option>
        <option value="duration">Durée</option>
      </select>

      <select v-model="filters.direction" class="input">
        <option value="asc">Asc</option>
        <option value="desc">Desc</option>
      </select>

      <button type="submit" class="btn">Filtrer</button>
      <button @click.prevent="reset" class="btn">Réinitialiser</button>
    </form>

    <ul class="space-y-4">
      <li v-for="show in shows.data" :key="show.id" class="border p-4 rounded shadow-sm">
        <div class="flex flex-col gap-1">
          <Link :href="route('show.show', show.id)" class="text-blue-600 hover:underline text-lg font-semibold">
            {{ show.title }}
          </Link>

          <div class="text-sm text-gray-600">
            <template v-if="!show.bookable">
              <em>Réservation indisponible</em>
            </template>
            <span v-if="show.representations_count === 1"> - 1 représentation</span>
            <span v-else-if="show.representations_count > 1"> - {{ show.representations_count }} représentations</span>
            <span v-else> - <em>aucune représentation</em></span>
          </div>

          <div class="mt-2">
            <ReserveButton
              :show-id="show.id"
              :bookable="show.bookable"
              :representations-count="show.representations_count"
            />
          </div>
        </div>
      </li>
    </ul>

    <Pagination :links="shows.links" />
  </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, reactive } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import ReserveButton from '@/Components/ReserveButton.vue'

const page = usePage()
const shows = page.props.shows
const user = page.props.auth.user
const filters = reactive({
  q: page.props.filters.q || '',
  min_duration: page.props.filters.min_duration || '',
  max_duration: page.props.filters.max_duration || '',
  postal_code: page.props.filters.postal_code || '',
  sort: page.props.filters.sort || '',
  direction: page.props.filters.direction || 'asc',
})

function filter() {
  router.get(route('show.index'), filters, { preserveScroll: true, preserveState: true })
}

function reset() {
  Object.assign(filters, {
    q: '',
    min_duration: '',
    max_duration: '',
    postal_code: '',
    sort: '',
    direction: 'asc'
  })
  filter()
}
</script>

<style scoped>
.input {
  margin-right: 0.5em;
  padding: 0.5em;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.btn {
  margin-right: 0.5em;
  padding: 0.5em 1em;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
