<template>
  <div class="max-w-2xl mx-auto py-10">
    <component
      :is="currentComponent"
      :form="form"
      @next="goToNextStep"
      @previous="goToPreviousStep"
      @submit="submitReservation"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Step1ChooseRepresentation from './Step1ChooseRepresentation.vue'
import Step2SeatsOrPlaces from './Step2SeatsOrPlaces.vue'
import Step3Delivery from './Step3Delivery.vue'
import Step4Payment from './Step4Payment.vue'
import Step5Confirmation from './Step5Confirmation.vue'

const steps = [
  Step1ChooseRepresentation,
  Step2SeatsOrPlaces,
  Step3Delivery,
  Step4Payment,
  Step5Confirmation,
]

const currentStep = ref(0)
const form = ref({
  representation_id: null,
  seats: 1,
  delivery_method: 'email',
  payment_method: null,
  user_info: {},
})

const currentComponent = computed(() => steps[currentStep.value])

function goToNextStep() {
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
  // Appel API via Inertia.post ou axios
  console.log('Formulaire soumis:', form.value)
}
</script>
