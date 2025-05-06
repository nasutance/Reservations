<template>
  <div>
    <button @click="toggle" class="p-2 rounded text-grey hover:underline transition">
      {{ isOpen ? closeLabel : openLabel }}
    </button>
    <div v-if="isOpen" class="mt-2">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  openLabel: {
    type: String,
    default: 'Voir'
  },
  closeLabel: {
    type: String,
    default: 'Masquer'
  }
});

const isOpen = ref(false);

function toggle() {
  isOpen.value = !isOpen.value;
}
</script>
