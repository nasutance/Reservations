<template>
  <!-- Étape 2 : choix du type de tarif et du nombre de places par tarif -->
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 2 : Choix des tarifs et nombre de places</h2>

    <!-- Liste des tarifs disponibles pour cette représentation -->
    <ul class="space-y-4 mb-6">
      <li
        v-for="price in show.prices"
        :key="price.id"
        class="border p-4 rounded shadow-sm flex justify-between items-center"
      >
        <div>
          <p class="font-semibold">{{ price.type }}</p>
          <p class="text-sm text-gray-600">{{ parseFloat(price.price).toFixed(2) }} €</p>
        </div>

        <!-- Champ pour indiquer le nombre de places souhaitées pour ce tarif -->
        <div class="flex items-center gap-2">
          <input
            type="number"
            min="0"
            class="w-16 border rounded px-2 py-1 text-right"
            v-model.number="quantitiesObj[price.id]"
          />
          <span>place(s)</span>
        </div>
      </li>
    </ul>

    <!-- Affichage du prix total estimé si au moins une place est sélectionnée -->
    <div class="text-right font-semibold text-lg" v-if="totalSeats > 0">
      Total estimé : {{ totalPrice.toFixed(2) }} €
    </div>

    <!-- Navigation : retour ou validation de l'étape si des places ont été choisies -->
    <div class="flex justify-between mt-8">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="totalSeats === 0" @click="validateStep" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
// Importation des fonctions de composition Vue
import { ref, computed, onMounted } from 'vue'

// Définition des propriétés attendues : le formulaire global et les infos du spectacle
const props = defineProps({
  form: Object,
  show: Object,
})

// Déclaration des événements émis par ce composant
const emit = defineEmits(['next', 'previous'])

// Stocke le nombre de places par tarif dans un objet réactif (clé = id du tarif)
const quantitiesObj = ref({})

// Initialisation du formulaire si des données existent déjà
onMounted(() => {
  props.show.prices.forEach(price => {
    quantitiesObj.value[price.id] = props.form.quantities?.find(q => q.price_id === price.id)?.quantity ?? 0
  })
})

// Calcule le nombre total de places sélectionnées (somme des quantités)
const totalSeats = computed(() =>
  Object.values(quantitiesObj.value).reduce((acc, val) => acc + (parseInt(val) || 0), 0)
)

// Calcule le prix total estimé en fonction des tarifs et quantités choisies
const totalPrice = computed(() =>
  props.show.prices.reduce((acc, price) => {
    const qty = quantitiesObj.value[price.id] || 0
    return acc + qty * parseFloat(price.price)
  }, 0)
)

// Prépare les données (quantités par tarif) à envoyer au parent pour l'étape suivante
function validateStep() {
  const result = props.show.prices
    .map(price => ({
      price_id: price.id,
      quantity: quantitiesObj.value[price.id] || 0,
    }))
    .filter(entry => entry.quantity > 0)

  emit('next', {
    quantities: result,
    seats: totalSeats.value,
  })
}
</script>

<style scoped>
/* Style du bouton principal (Suivant) */
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

/* Style du bouton secondaire (Précédent) */
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
