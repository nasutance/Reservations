<template>
  <div>
    <h2 class="text-xl font-semibold mb-6">Étape 5 : Confirmation de votre réservation</h2>

    <div class="mb-6 space-y-4 text-gray-800">
      <p><strong>Spectacle :</strong> {{ show.title }}</p>
      <p><strong>Représentation :</strong>
        {{ formatDate(selectedRepresentation.schedule) }} – {{ selectedRepresentation.location?.designation || 'Lieu inconnu' }}
      </p>

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

      <p class="text-right text-lg font-semibold mt-4">
        Total estimé : {{ totalPrice.toFixed(2) }} €
      </p>
    </div>

    <div class="flex justify-between items-center mt-8">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>

      <div>
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
import { ref, computed } from 'vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  form: Object,
  show: Object,
  paypalClientId: String,
})

const reservationId = ref(null)
const reservationCreated = ref(false)

const selectedRepresentation = computed(() =>
  props.show.representations.find(r => r.id === props.form.representation_id) || {}
)

function getPrice(priceId) {
  const p = props.show.prices.find(p => p.id === priceId)
  return p ? parseFloat(p.price) : 0
}

function priceLabel(priceId) {
  const p = props.show.prices.find(p => p.id === priceId)
  return p ? p.description : 'Tarif inconnu'
}

const totalPrice = computed(() =>
  props.form.quantities.reduce((acc, item) =>
    acc + item.quantity * getPrice(item.price_id), 0)
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
        Inertia.patch(`/reservation/${reservationId.value}`, {
          status: 'payée',
        }, {
          onSuccess: () => {
            Inertia.visit('/dashboard')
          }
        })
      })
    }
  }).render('#paypal-button-container')
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
