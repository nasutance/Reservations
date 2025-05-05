<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 2 : Choix des tarifs et nombre de places</h2>

    <ul class="space-y-4 mb-6">
      <li
        v-for="price in show.prices"
        :key="price.id"
        class="border p-4 rounded shadow-sm flex justify-between items-center"
      >
        <div>
          <p class="font-semibold">{{ price.type }}</p>
          <p class="text-sm text-gray-600">{{ parseFloat(price.price).toFixed(2) }} €</p>
        </div>

        <div class="flex items-center gap-2">
          <input
            type="number"
            min="0"
            class="w-16 border rounded px-2 py-1 text-right"
            v-model.number="quantities[price.id]"
          />
          <span>place(s)</span>
        </div>
      </li>
    </ul>

    <div class="text-right font-semibold text-lg" v-if="totalSeats > 0">
      Total estimé : {{ totalPrice.toFixed(2) }} €
    </div>

    <div class="flex justify-between mt-8">
      <button @click="$emit('previous')" class="btn-secondary">Précédent</button>
      <button :disabled="totalSeats === 0" @click="validateStep" class="btn">Suivant</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  form: Object,
  show: Object,
})

const emit = defineEmits(['next', 'previous'])

const quantities = ref({})

onMounted(() => {
  props.show.prices.forEach(price => {
    quantities.value[price.id] = props.form.quantities?.[price.id] ?? 0
  })
})

const totalSeats = computed(() =>
  Object.values(quantities.value).reduce((acc, val) => acc + (parseInt(val) || 0), 0)
)

const totalPrice = computed(() =>
  props.show.prices.reduce((acc, price) => {
    const qty = quantities.value[price.id] || 0
    return acc + qty * parseFloat(price.price)
  }, 0)
)

function validateStep() {
  const result = props.show.prices
    .map(price => ({
      price_id: price.id,
      quantity: quantities.value[price.id] || 0
    }))
    .filter(entry => entry.quantity > 0)

  emit('next', {
    quantities: result,
    seats: totalSeats.value,
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
