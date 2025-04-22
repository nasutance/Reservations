<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 3 : Méthode de livraison</h2>

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

    <div class="flex justify-between">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="!selectedMethod" @click="goNext" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  form: Object,
})

const selectedMethod = ref(props.form.delivery_method || 'email')

function goNext() {
  emit('next', { delivery_method: selectedMethod.value })
}

const emit = defineEmits(['next', 'previous'])

watch(() => props.form.delivery_method, (newVal) => {
  if (newVal) selectedMethod.value = newVal
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
