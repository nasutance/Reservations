<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 4 : Mode de paiement</h2>

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

    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="!selectedPayment" @click="goNext" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  form: Object,
})

const selectedPayment = ref(props.form.payment_method || null)

const emit = defineEmits(['next', 'previous'])

function goNext() {
  if (selectedPayment.value) {
    emit('next', { payment_method: selectedPayment.value })
  }
}

watch(() => props.form.payment_method, (newVal) => {
  if (newVal) selectedPayment.value = newVal
})
</script>

<style scoped>
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
.btn-secondary {
  background-color: #e5e7eb;
  color: #111827;
  padding: 0.6em 1.2em;
  border-radius: 0.375rem;
  font-weight: 600;
}
</style>
