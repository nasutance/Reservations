<template>
  <!-- Étape 3 : choix du mode de livraison des billets -->
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 3 : Méthode de livraison</h2>

    <!-- Options de livraison disponibles : email, téléchargement ou retrait -->
    <div class="space-y-4 mb-6">
      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="email" v-model="selectedMethod" />
        Envoi par email (gratuit)
      </label>

      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="download" v-model="selectedMethod" />
        Téléchargement PDF (gratuit)
      </label>

      <label class="flex items-center gap-3 cursor-pointer">
        <input type="radio" value="pickup" v-model="selectedMethod" />
        Retrait au guichet (avant la représentation)
      </label>
    </div>

    <!-- Navigation entre les étapes -->
    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="!selectedMethod" @click="goNext" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
// Import des outils de réactivité de Vue
import { ref, watch } from 'vue'

// Déclaration des props reçues du parent (formulaire global)
const props = defineProps({
  form: Object,
})

// Valeur sélectionnée pour la méthode de livraison, initialisée avec la valeur existante
const selectedMethod = ref(props.form.delivery_method || 'email')

// Événements émis par le composant (navigation dans le formulaire)
const emit = defineEmits(['next', 'previous'])

// Fonction appelée pour passer à l'étape suivante en transmettant la méthode choisie
function goNext() {
  emit('next', { delivery_method: selectedMethod.value })
}

// Mise à jour locale si le formulaire global est modifié de l'extérieur
watch(() => props.form.delivery_method, (newVal) => {
  if (newVal) selectedMethod.value = newVal
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
