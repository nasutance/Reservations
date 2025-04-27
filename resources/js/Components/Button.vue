<template>
  <button
    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>

</script>
