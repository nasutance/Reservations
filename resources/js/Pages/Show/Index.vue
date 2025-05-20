<template>
  <!-- Layout principal de l'application -->
  <AppLayout>
    <div>
      <!-- Titre de la page -->
      <h1 class="text-2xl font-bold mb-4">Liste des spectacles</h1>

      <!-- Formulaire de filtres (mot-clé, durée, tri, etc.) -->
      <!-- Le submit appelle la méthode filter() sans recharger la page -->
      <form @submit.prevent="filter" class="space-y-2 mb-6">
        <input type="text" v-model="filters.q" placeholder="Mot-clé" class="input" />
        <input type="number" v-model="filters.min_duration" placeholder="Durée min" class="input" />
        <input type="number" v-model="filters.max_duration" placeholder="Durée max" class="input" />
        <input type="text" v-model="filters.postal_code" placeholder="Code postal" class="input" />

        <!-- Tri par titre ou durée -->
        <select v-model="filters.sort" class="input">
          <option value="">-- Tri --</option>
          <option value="title">Titre</option>
          <option value="duration">Durée</option>
        </select>

        <!-- Sens du tri : ascendant ou descendant -->
        <select v-model="filters.direction" class="input">
          <option value="asc">Asc</option>
          <option value="desc">Desc</option>
        </select>

        <!-- Bouton pour appliquer les filtres -->
        <button type="submit" class="btn">Filtrer</button>

        <!-- Bouton pour réinitialiser les filtres -->
        <!-- Appelle reset(), qui vide tous les champs et relance la recherche -->
        <button @click.prevent="reset" class="btn">Réinitialiser</button>
      </form>

      <!-- Liste des spectacles -->
      <ul class="space-y-4">
        <!-- Boucle sur les spectacles renvoyés par le backend -->
        <li v-for="show in shows.data" :key="show.id" class="border p-4 rounded shadow-sm">
          <div class="flex flex-col gap-1">
            <!-- Lien vers la page de détail du spectacle -->
            <Link :href="route('show.show', show.id)" class="text-blue-600 hover:underline text-lg font-semibold">
              {{ show.title }}
            </Link>

            <!-- Informations sur la réservation et le nombre de représentations -->
            <div class="text-sm text-gray-600">
              <!-- Indication si le spectacle n'est pas réservable -->
              <template v-if="!show.bookable">
                <em>Réservation indisponible</em>
              </template>
              <!-- Affiche le nombre de représentations -->
              <span v-if="show.representations_count === 1"> - 1 représentation</span>
              <span v-else-if="show.representations_count > 1"> - {{ show.representations_count }} représentations</span>
              <span v-else> - <em>aucune représentation</em></span>
            </div>

            <!-- Bouton personnalisé pour réserver -->
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

      <!-- Composant de pagination (liens générés par Laravel) -->
      <Pagination :links="shows.links" />
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { reactive, computed } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import ReserveButton from '@/Components/ReserveButton.vue'

// Accès aux props renvoyées par le backend via Inertia
const page = usePage()

// Computed qui garde les données "shows" à jour à chaque navigation Inertia
const shows = computed(() => page.props.shows)

// Filtres réactifs, initialisés avec les valeurs passées par le backend (persistées dans l’URL)
const filters = reactive({
  q: page.props.filters.q || '',
  min_duration: page.props.filters.min_duration || '',
  max_duration: page.props.filters.max_duration || '',
  postal_code: page.props.filters.postal_code || '',
  sort: page.props.filters.sort || '',
  direction: page.props.filters.direction || 'asc',
})

// Fonction appelée quand on soumet le formulaire
// Fait un GET Inertia avec les filtres comme query params
function filter() {
  router.get(route('show.index'), filters, {
    preserveScroll: true, // évite de remonter en haut de la page
    preserveState: true,  // garde l’état des composants inchangé (si besoin)
  })
}

// Réinitialise tous les filtres à leur valeur par défaut puis relance la recherche
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
/* Style pour les inputs */
.input {
  margin-right: 0.5em;
  padding: 0.5em;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Style pour les boutons */
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
