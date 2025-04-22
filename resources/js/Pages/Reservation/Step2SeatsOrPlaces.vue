<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 2 : Nombre de places</h2>

    <div class="mb-6">
      <label for="seats" class="block mb-2">Combien de places souhaitez-vous réserver ?</label>
      <input
        type="number"
        id="seats"
        v-model.number="seats"
        min="1"
        class="w-full border rounded px-3 py-2"
      />
    </div>

    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="seats < 1" @click="validateStep" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  form: Object,
})

const seats = ref(props.form.seats ?? 1)

function validateStep() {
  if (seats.value >= 1) {
    emit('next', { seats: seats.value })
  }
}

const emit = defineEmits(['next', 'previous'])

// synchronise avec le form global si l’utilisateur navigue avant/arrière
watch(() => props.form.seats, (newVal) => {
  if (newVal) seats.value = newVal
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
