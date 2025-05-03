<template>
  <div class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-xl shadow-md w-[400px] max-w-full">
      <h2 class="text-lg font-bold mb-4">Créer {{ entityLabels[entity] || entity }}</h2>
      <form @submit.prevent="submit">
        <div v-for="field in fields" :key="field.name" class="mb-4">
          <label :for="field.name" class="block font-semibold mb-1">{{ field.label }}</label>

          <input
            v-if="field.type === 'text' || field.type === 'email' || field.type === 'number'"
            :type="field.type"
            v-model="form[field.name]"
            :id="field.name"
            class="w-full border px-3 py-2 rounded"
          />

          <select
            v-else-if="field.type === 'select'"
            v-model="form[field.name]"
            :id="field.name"
            class="w-full border px-3 py-2 rounded"
          >
            <option value="" disabled>-- Choisir --</option>
            <option v-for="opt in field.options" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-300 rounded">Annuler</button>
          <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Créer</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  entity: String,
  fields: Array,
  submitUrl: String,
})

const entityLabels = {
  artist: 'artiste',
  user: 'utilisateur',
  reservation: 'réservation',
  // Ajoute les autres entités ici si nécessaire
}

const emit = defineEmits(['close'])

const form = reactive({})

// Initialiser les champs
props.fields.forEach(field => {
  form[field.name] = ''
})

function submit() {
  router.post(props.submitUrl, form, {
    preserveScroll: true,
    onSuccess: () => {
  emit('close')
  router.visit(route('dashboard'), { preserveScroll: true })}

  })
}
</script>
