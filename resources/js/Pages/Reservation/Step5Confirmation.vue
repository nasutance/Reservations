<template>
  <!-- Étape 5 : résumé des choix et confirmation finale de la réservation -->
  <div>
    <h2 class="text-xl font-semibold mb-6">Étape 5 : Confirmation de votre réservation</h2>

    <!-- Récapitulatif des données saisies par l'utilisateur -->
    <div class="mb-6 space-y-4 text-gray-800">
      <p><strong>Spectacle :</strong> {{ show.title }}</p>
      <p><strong>Représentation :</strong>
        {{ formatDate(selectedRepresentation.schedule) }} – {{ selectedRepresentation.location?.designation || 'Lieu inconnu' }}
      </p>

      <!-- Liste des tarifs sélectionnés avec quantité et sous-total -->
      <div>
        <strong>Tarifs sélectionnés :</strong>
        <ul class="mt-2 space-y-2">
          <li v-for="item in form.quantities" :key="item.price_id" class="flex justify-between">
            <span>{{ priceLabel(item.price_id) }} × {{ item.quantity }} place(s)</span>
            <span>{{ (getPrice(item.price_id) * item.quantity).toFixed(2) }} €</span>
          </li>
        </ul>
      </div>

      <p><strong>Livraison :</strong> {{ deliveryLabels[form.delivery_method] || 'N/A' }}</p>
      <p><strong>Paiement :</strong> {{ paymentLabels[form.payment_method] || 'N/A' }}</p>

      <!-- Affichage du total général -->
      <p class="text-right text-lg font-semibold mt-4">
        Total estimé : {{ totalPrice.toFixed(2) }} €
      </p>
    </div>

    <!-- Boutons pour confirmer ou revenir à l'étape précédente -->
    <div class="flex justify-between items-center mt-8">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>

      <div>
        <!-- Affichage du bouton de confirmation ou des instructions de paiement -->
        <button
          v-if="!reservationCreated"
          class="btn"
          @click="confirmReservation"
        >
          Confirmer la réservation
        </button>

        <div v-if="reservationCreated">
          <p class="text-sm text-green-600 mb-2">
            Réservation enregistrée sous le numéro <strong>#{{ reservationId }}</strong>. Procédez au paiement :
          </p>
          <div id="paypal-button-container"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Importation des outils Vue et des dépendances
import { ref, computed } from 'vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

// Récupération des props du composant parent
const props = defineProps({
  form: Object,
  show: Object,
  paypalClientId: String,
})

// Références réactives pour suivre l'état de la réservation
const reservationId = ref(null)
const reservationCreated = ref(false)

// Recherche de la représentation choisie à partir de l'ID
const selectedRepresentation = computed(() =>
  props.show.representations.find(r => r.id === props.form.representation_id) || {}
)

// Fonction utilitaire : récupérer le prix d'un tarif via son ID
function getPrice(priceId) {
  const p = props.show.prices.find(p => p.id === priceId)
  return p ? parseFloat(p.price) : 0
}

// Fonction utilitaire : récupérer le libellé d'un tarif
function priceLabel(priceId) {
  const p = props.show.prices.find(p => p.id === priceId)
  return p ? p.type : 'Tarif inconnu'
}

// Calcul dynamique du total à payer
const totalPrice = computed(() =>
  props.form.quantities.reduce((acc, item) =>
    acc + item.quantity * getPrice(item.price_id), 0)
)

// Dictionnaires de correspondance pour affichage user-friendly
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

// Formate la date/heure d'une représentation en format local FR
function formatDate(iso) {
  return new Date(iso).toLocaleString('fr-BE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Envoie les données de réservation au backend
function confirmReservation() {
  axios.post('/reservation', {
    representation_id: props.form.representation_id,
    quantities: props.form.quantities,
    delivery_method: props.form.delivery_method,
    payment_method: props.form.payment_method,
    status: 'en attente',
  })
  .then(response => {
    reservationId.value = response.data.lastInsertedId
    reservationCreated.value = true
    loadPaypal()
  })
  .catch(error => {
    console.error("Erreur création réservation :", error)
  })
}

// Charge le script PayPal uniquement si nécessaire
function loadPaypal() {
  if (!window.paypal && !document.getElementById('paypal-sdk')) {
    const script = document.createElement('script')
    script.src = `https://www.paypal.com/sdk/js?client-id=${props.paypalClientId}&currency=EUR`
    script.id = "paypal-sdk"
    script.onload = renderPaypal
    document.body.appendChild(script)
  } else if (window.paypal) {
    renderPaypal()
  }
}

// Initialise les boutons PayPal avec les callbacks de paiement
function renderPaypal() {
  window.paypal.Buttons({
    createOrder: (data, actions) => {
      return actions.order.create({
        purchase_units: [{
          amount: {
            value: totalPrice.value.toFixed(2)
          }
        }]
      })
    },
    onApprove: (data, actions) => {
      return actions.order.capture().then(() => {
        axios.patch(`/reservation/${reservationId.value}`, {
          status: 'payée',
        })
        .then(() => {
          Inertia.visit(`/merci?reservationId=${reservationId.value}`)
        })
        .catch((err) => {
          console.error("Erreur PATCH statut :", err)
        })
      })
    }
  }).render('#paypal-button-container')
}
</script>

<style scoped>
/* Style du bouton principal (valider la réservation) */
.btn {
  background-color: #4f46e5;
  color: white;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}

/* Style du bouton secondaire (retour) */
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
