<template>
<AppLayout>
  <div class="max-w-2xl mx-auto py-10">
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
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
const { props } = usePage()
const show = props.show
const paypalClientId = props.paypalClientId

import { ref, computed } from 'vue'
import Step1ChooseRepresentation from './Step1ChooseRepresentation.vue'
import Step2SeatsAndPrice from './Step2SeatsAndPrice.vue'
import Step3Delivery from './Step3Delivery.vue'
import Step4Payment from './Step4Payment.vue'
import Step5Confirmation from './Step5Confirmation.vue'

const steps = [
  Step1ChooseRepresentation,
  Step2SeatsAndPrice,
  Step3Delivery,
  Step4Payment,
  Step5Confirmation,
]

const currentStep = ref(0)
const form = ref({
  representation_id: null,
  quantities: {}, // tableau d’objets {price_id, quantity}
  price_id: null, // tarif principal sélectionné
  seats: 0,
  delivery_method: 'email',
  payment_method: null,
  user_info: {},
})


const currentComponent = computed(() => steps[currentStep.value])

function goToNextStep(payload = {}) {
  Object.assign(form.value, payload)
  if (currentStep.value < steps.length - 1) {
    currentStep.value++
  }
}

function goToPreviousStep() {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

function submitReservation() {
  console.log('Formulaire soumis:', form.value)
}
</script>
