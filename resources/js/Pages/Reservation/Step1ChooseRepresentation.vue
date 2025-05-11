<template>
  <!-- Étape 1 du processus : l'utilisateur choisit une représentation -->
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 1 : Choix de la représentation</h2>

    <!-- Liste des représentations disponibles pour le spectacle -->
    <ul class="space-y-3 mb-6">
      <li v-for="rep in show.representations" :key="rep.id" class="border p-3 rounded shadow-sm hover:bg-gray-100">
        <label class="flex items-center gap-4 cursor-pointer">
          <!-- Sélection d'une représentation via un bouton radio -->
          <input
            type="radio"
            :value="rep.id"
            v-model="selectedId"
            class="form-radio text-indigo-600"
          />
          <span>
            {{ formatDate(rep.schedule) }}
            <!-- Affichage du lieu si présent -->
            <template v-if="rep.location"> – {{ rep.location.designation }}</template>
          </span>
        </label>
      </li>
    </ul>

    <!-- Bouton pour passer à l'étape suivante, désactivé tant qu'aucune représentation n'est sélectionnée -->
    <button
      :disabled="!selectedId"
      @click="goToNextStep"
      class="btn"
    >
      Suivant
    </button>
  </div>
</template>

<script setup>
// Import des outils de composition de Vue
import { ref } from 'vue'
// Import de la librairie dayjs pour le formatage de date
import dayjs from 'dayjs'

// Définition des propriétés reçues du composant parent
const props = defineProps({
  form: Object,
  show: Object,
})

// Référence réactive pour stocker l'identifiant de la représentation sélectionnée
const selectedId = ref(null)

// Fonction appelée lors du clic sur "Suivant"
function goToNextStep() {
  if (selectedId.value) {
    // Émet l'événement "next" au parent avec l'ID de la représentation choisie
    emit('next', { representation_id: selectedId.value })
  }
}

// Fonction utilitaire pour formater la date d'une représentation
function formatDate(isoString) {
  return dayjs(isoString).format('DD/MM/YYYY à HH:mm')
}

// Déclaration de l'événement que ce composant peut émettre
const emit = defineEmits(['next'])
</script>

<style scoped>
/* Style personnalisé pour le bouton "Suivant" */
.btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
  transition: background-color 0.2s ease;
}
.btn:disabled {
  background-color: #cbd5e1;
  cursor: not-allowed;
}
</style>
