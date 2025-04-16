<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Étape 1 : Choix de la représentation</h2>

    <ul class="space-y-3 mb-6">
      <li v-for="rep in show.representations" :key="rep.id" class="border p-3 rounded shadow-sm hover:bg-gray-100">
        <label class="flex items-center gap-4 cursor-pointer">
          <input
            type="radio"
            :value="rep.id"
            v-model="selectedId"
            class="form-radio text-indigo-600"
          />
          <span>
            {{ formatDate(rep.schedule) }}
            <template v-if="rep.location"> – {{ rep.location.designation }}</template>
          </span>
        </label>
      </li>
    </ul>

    <button
      :disabled="!selectedId"
      @click="goToNextStep"
      class="btn"
    >
      Suivant
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
  form: Object,
  show: Object,
})

const selectedId = ref(null)

function goToNextStep() {
  if (selectedId.value) {
    // On émet l'ID de la représentation choisie au composant parent
    emit('next', { representation_id: selectedId.value })
  }
}

function formatDate(isoString) {
  return dayjs(isoString).format('DD/MM/YYYY à HH:mm')
}

const emit = defineEmits(['next'])
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
</style>
