<template>
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
      <li v-for="representation in show.representations" :key="representation.id">
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
</template>

<script setup>
import { usePage, Link } from '@inertiajs/vue3'

const page = usePage()
const show = page.props.show
const collaborateurs = page.props.collaborateurs
</script>
