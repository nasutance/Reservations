<template>
  <div>
    <h2 class="text-xl font-semibold mb-6">Étape 5 : Confirmation de votre réservation</h2>

    <div class="mb-6 space-y-2 text-gray-800">
      <p><strong>Spectacle :</strong> {{ show.title }}</p>
      <p><strong>Représentation :</strong> {{ formatDate(selectedRepresentation.schedule) }} – {{ selectedRepresentation.location?.designation || 'Lieu inconnu' }}</p>
      <p><strong>Nombre de places :</strong> {{ form.seats }}</p>
      <p><strong>Livraison :</strong> {{ deliveryLabels[form.delivery_method] || 'N/A' }}</p>
      <p><strong>Paiement :</strong> {{ paymentLabels[form.payment_method] || 'N/A' }}</p>
    </div>

    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button @click="$emit('submit')" class="btn">Procéder au paiement</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: Object,
  show: Object,
})

// Trouve la représentation choisie à partir de son ID
const selectedRepresentation = computed(() =>
  props.show.representations.find(r => r.id === props.form.representation_id) || {}
)

const deliveryLabels = {
  email: 'Envoi par email',
  download: 'Téléchargement PDF',
  pickup: 'Retrait au guichet',
}

const paymentLabels = {
  card: 'Carte bancaire',
  transfer: 'Virement bancaire',
  on_site: 'Paiement sur place',
}

function formatDate(iso) {
  return new Date(iso).toLocaleString('fr-BE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<style scoped>
.btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
