<template>
<AppLayout>
  <article>
    <h1 class="text-2xl font-bold mb-4">{{ show.title }}</h1>

    <div class="mb-4">
      <img v-if="show.poster_url" :src="`/images/${show.poster_url}`" :alt="show.title" width="200" />
      <canvas v-else width="200" height="100" class="border"></canvas>
    </div>

    <p v-if="show.location">
      <strong>Lieu de création :</strong> {{ show.location.designation }}
    </p>

    <p><strong>Durée :</strong> {{ show.duration }} minutes</p>
    <p><strong>Année de création :</strong> {{ show.created_in }}</p>

    <p>
      <em>{{ show.bookable ? 'Réservable' : 'Non réservable' }}</em>
    </p>

    <h2 class="mt-6 font-semibold">Liste des représentations</h2>
    <ul v-if="show.representations.length">
      <li v-for="representation in show.representations" :key="representation.id" class="mb-2">
        <div>
          {{ representation.schedule }}
          <span v-if="representation.location">
            ({{ representation.location.designation }})
          </span>
          <span v-else-if="show.location">
            ({{ show.location.designation }})
          </span>
          <span v-else>
            (lieu à déterminer)
          </span>
        </div>
        <div class="mt-1">
          <ReserveButton
            :show-id="show.id"
            :bookable="show.bookable"
            :representations-count="show.representations.length"
          />
        </div>
      </li>
    </ul>
    <p v-else>Aucune représentation</p>

    <h2 class="mt-6 font-semibold">Liste des artistes</h2>

    <p><strong>Auteur :</strong>
      <span v-for="(auteur, index) in collaborateurs.auteur" :key="index">
        {{ auteur.firstname }} {{ auteur.lastname }}
        <template v-if="index === collaborateurs.auteur.length - 2"> et </template>
        <template v-else-if="index < collaborateurs.auteur.length - 1">, </template>
      </span>
    </p>

    <p><strong>Metteur en scène :</strong>
      <span v-for="(sceno, index) in collaborateurs['scénographe']" :key="index">
        {{ sceno.firstname }} {{ sceno.lastname }}
        <template v-if="index === collaborateurs['scénographe'].length - 2"> et </template>
        <template v-else-if="index < collaborateurs['scénographe'].length - 1">, </template>
      </span>
    </p>

    <p><strong>Distribution :</strong>
      <span v-for="(comedien, index) in collaborateurs.comédien" :key="index">
        {{ comedien.firstname }} {{ comedien.lastname }}
        <template v-if="index === collaborateurs.comédien.length - 2"> et </template>
        <template v-else-if="index < collaborateurs.comédien.length - 1">, </template>
      </span>
    </p>
  </article>

  <nav class="mt-6">
    <Link :href="route('show.index')" class="text-blue-600 hover:underline">
      Retour à l'index
    </Link>
  </nav>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage, useForm, Link } from '@inertiajs/vue3'
import ReserveButton from '@/Components/ReserveButton.vue'
import { computed } from 'vue'

const page = usePage()
const show = page.props.show
const user = page.props.auth.user

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

</script>

<style scoped>
.btn {
  margin-top: 0.3em;
  padding: 0.4em 0.8em;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
