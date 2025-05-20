<template>
  <!-- Layout principal de l'application -->
  <AppLayout>
    <article>
      <!-- Titre du spectacle -->
      <h1 class="text-2xl font-bold mb-4">{{ show.title }}</h1>

      <!-- Affiche du spectacle si dispo, sinon canvas vide -->
      <div class="mb-4">
        <img v-if="show.poster_url" :src="`/images/${show.poster_url}`" :alt="show.title" width="200" />
        <canvas v-else width="200" height="100" class="border"></canvas>
      </div>

      <!-- Détails du spectacle -->
      <p v-if="show.location">
        <strong>Lieu de création :</strong> {{ show.location.designation }}
      </p>

      <p><strong>Durée :</strong> {{ show.duration }} minutes</p>
      <p><strong>Année de création :</strong> {{ show.created_in }}</p>

      <p>
        <em>{{ show.bookable ? 'Réservable' : 'Non réservable' }}</em>
      </p>

      <h2 class="mt-6 font-semibold">Mots-clés</h2>
      <ul v-if="show.tags && show.tags.length" class="list-disc list-inside">
        <li v-for="tag in show.tags" :key="tag.id">{{ tag.tag }}</li>
      </ul>
      <p v-else>Aucun mot-clé associé.</p>

      <div v-if="user && user.role === 'admin'" class="mt-4">
        <form @submit.prevent="submitTag">
          <label for="tag_id" class="block font-semibold mb-1">Ajouter un mot-clé</label>
          <select v-model="form.tag_id" id="tag_id" class="border p-2 rounded mb-2">
            <option v-for="tag in allTags" :value="tag.id" :key="tag.id">
              {{ tag.tag }}
            </option>
          </select>
          <button type="submit" class="btn">Ajouter</button>
        </form>
      </div>

      <!-- Liste des vidéos -->
       <h2 class="mt-6 font-semibold">Vidéos</h2>
       <div v-if="show.videos && show.videos.length">
        <div v-for="video in show.videos" :key="video.id" class="mb-4">
          <h3 class="text-lg font-semibold">{{ video.title }}</h3>
          <iframe
          class="w-full max-w-md"
          height="315"
          :src="video.video_url"
          frameborder="0"
          allowfullscreen
          ></iframe>
        </div>
      </div>
      <p v-else>Aucune vidéo pour ce spectacle.</p>

      <div v-if="user && user.role === 'admin'" class="mt-4">
        <form @submit.prevent="submitVideo">
          <label for="title" class="block font-semibold mb-1">Titre de la vidéo</label>
          <input v-model="videoForm.title" type="text" class="border p-2 rounded mb-2 w-full" />

          <label for="video_url" class="block font-semibold mb-1">URL (YouTube embed)</label>
          <input v-model="videoForm.video_url" type="url" class="border p-2 rounded mb-2 w-full" />

          <button type="submit" class="btn">Ajouter la vidéo</button>
        </form>
      </div>


      <!-- Liste des représentations du spectacle -->
      <h2 class="mt-6 font-semibold">Liste des représentations</h2>
      <ul v-if="show.representations.length">
        <li v-for="representation in show.representations" :key="representation.id" class="mb-2">
          <div>
            <!-- Date formatée de la représentation -->
            {{ formatDate(representation.schedule) }}

            <!-- Lieu de représentation : d'abord spécifique, sinon lieu du spectacle, sinon inconnu -->
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

          <!-- Bouton pour réserver (composant personnalisé) -->
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

      <!-- Liste des artistes impliqués -->
      <h2 class="mt-6 font-semibold">Liste des artistes</h2>

      <!-- Auteur(s) -->
      <p><strong>Auteur :</strong>
        <span v-for="(auteur, index) in collaborateurs.auteur" :key="index">
          {{ auteur.firstname }} {{ auteur.lastname }}
          <!-- Formattage type "Dupont, Durand et Martin" -->
          <template v-if="index === collaborateurs.auteur.length - 2"> et </template>
          <template v-else-if="index < collaborateurs.auteur.length - 1">, </template>
        </span>
      </p>

      <!-- Scénographe(s) -->
      <p><strong>Metteur en scène :</strong>
        <span v-for="(sceno, index) in collaborateurs['scénographe']" :key="index">
          {{ sceno.firstname }} {{ sceno.lastname }}
          <template v-if="index === collaborateurs['scénographe'].length - 2"> et </template>
          <template v-else-if="index < collaborateurs['scénographe'].length - 1">, </template>
        </span>
      </p>

      <!-- Comédien(ne)s -->
      <p><strong>Distribution :</strong>
        <span v-for="(comedien, index) in collaborateurs.comédien" :key="index">
          {{ comedien.firstname }} {{ comedien.lastname }}
          <template v-if="index === collaborateurs.comédien.length - 2"> et </template>
          <template v-else-if="index < collaborateurs.comédien.length - 1">, </template>
        </span>
      </p>
    </article>

    <!-- Lien retour à la liste des spectacles -->
    <nav class="mt-6">
      <Link :href="route('show.index')" class="text-blue-600 hover:underline">
        Retour à l'index
      </Link>
    </nav>
  </AppLayout>
</template>

<script setup>

// Import du layout principal
import AppLayout from '@/Layouts/AppLayout.vue'

// Fonctions d'Inertia.js
import { usePage, useForm, Link } from '@inertiajs/vue3'

// Composant pour le bouton de réservation
import ReserveButton from '@/Components/ReserveButton.vue'

// Utilisation de propriétés réactives
import { computed } from 'vue'

// Fonction utilitaire pour formater les dates
import { formatDate } from '@/utils/formatDate.js'

// Récupération des props envoyées par le backend via Inertia
const page = usePage()
const show = computed(() => page.props.show)
const user = page.props.auth.user
const allTags = page.props.allTags
const form = useForm({ tag_id: '' })


// Traitement des artistes associés au spectacle par type
// On trie les artistes dans un objet { auteur: [...], scénographe: [...], comédien: [...] }
const collaborateurs = computed(() => {
  const mapping = {
    auteur: [],
    scénographe: [],
    comédien: [],
  }

  // Boucle sur les types d'artistes associés au spectacle
  for (const at of show.value.artist_types ?? []) {
    const type = at.type?.type
    if (mapping[type]) {
      mapping[type].push(at.artist)
    }
  }

  return mapping
})

const videoForm = useForm({
  title: '',
  video_url: '',
})

function submitVideo() {
  videoForm.post(route('videos.store'), {
    preserveScroll: true,
    onSuccess: () => videoForm.reset(),
  })
}


function submitTag() {
  form.post(route('show.attachTag', show.value.id), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

</script>

<style scoped>
/* Style du bouton de réservation */
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
