<template>
  <!-- Étape 4 : choix du mode de paiement -->
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 4 : Mode de paiement</h2>

    <!-- Liste des options de paiement disponibles -->
    <div class="space-y-4 mb-6">
      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="card" v-model="selectedPayment" />
        Paiement en ligne (carte bancaire)
      </label>

      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="transfer" v-model="selectedPayment" />
        Virement bancaire
      </label>

      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="on_site" v-model="selectedPayment" />
        Paiement sur place
      </label>
    </div>

    <!-- Navigation entre les étapes -->
    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="!selectedPayment" @click="goNext" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
// Import des outils de composition de Vue
import { ref, watch } from 'vue'

// Déclaration des propriétés : le formulaire global
const props = defineProps({
  form: Object,
})

// Valeur réactive représentant le mode de paiement sélectionné
const selectedPayment = ref(props.form.payment_method || null)

// Déclaration des événements émis
const emit = defineEmits(['next', 'previous'])

// Fonction déclenchée pour passer à l'étape suivante avec le mode choisi
function goNext() {
  if (selectedPayment.value) {
    emit('next', { payment_method: selectedPayment.value })
  }
}

// Mise à jour locale si le formulaire global est modifié depuis l’extérieur
watch(() => props.form.payment_method, (newVal) => {
  if (newVal) selectedPayment.value = newVal
})
</script>

<style scoped>
/* Style du bouton "Suivant" */
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

/* Style du bouton "Précédent" */
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
