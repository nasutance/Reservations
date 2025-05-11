<template>
  <!-- Layout principal de la page contenant le formulaire multi-étapes -->
  <AppLayout>
    <div class="max-w-2xl mx-auto py-10">
      <!-- Affichage dynamique du composant en fonction de l'étape courante -->
      <component
        :is="currentComponent"
        :form="form"
        :show="show"
        :paypal-client-id="paypalClientId"
        @next="goToNextStep"
        @previous="goToPreviousStep"
        @submit="submitReservation"
      />
    </div>
  </AppLayout>
</template>

<script setup>
// Import du layout principal
import AppLayout from '@/Layouts/AppLayout.vue'

// Accès aux propriétés passées depuis le backend via Inertia
import { usePage } from '@inertiajs/vue3'

// Composition API de Vue pour créer des références réactives
import { ref, computed } from 'vue'

// Import des composants représentant chaque étape du processus de réservation
import Step1ChooseRepresentation from './Step1ChooseRepresentation.vue'
import Step2SeatsAndPrice from './Step2SeatsAndPrice.vue'
import Step3Delivery from './Step3Delivery.vue'
import Step4Payment from './Step4Payment.vue'
import Step5Confirmation from './Step5Confirmation.vue'

// Récupération des propriétés (spectacle et identifiant PayPal)
const { props } = usePage()
const show = props.show
const paypalClientId = props.paypalClientId

// Tableau contenant les composants représentant les différentes étapes
const steps = [
  Step1ChooseRepresentation,
  Step2SeatsAndPrice,
  Step3Delivery,
  Step4Payment,
  Step5Confirmation,
]

// Étape actuelle (commence à 0)
const currentStep = ref(0)

// Composant correspondant à l'étape courante (calculé dynamiquement)
const currentComponent = computed(() => steps[currentStep.value])

// Objet réactif contenant les données du formulaire de réservation
const form = ref({
  representation_id: null,
  quantities: [], // Tableau de quantités par catégorie
  seats: 0,
  delivery_method: 'email',
  payment_method: null,
  user_info: {}, // Informations utilisateur éventuelles
})

// Fonction pour passer à l'étape suivante, en fusionnant les nouvelles données
function goToNextStep(payload = {}) {
  form.value = {
    ...form.value,
    ...payload,
  }

  if (currentStep.value < steps.length - 1) {
    currentStep.value++
  }
}

// Fonction pour revenir à l'étape précédente
function goToPreviousStep() {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

// Fonction appelée à la dernière étape pour soumettre la réservation
function submitReservation() {
  console.log('Formulaire soumis:', form.value)
}
</script>
