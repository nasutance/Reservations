<template>
  <div class="flex flex-wrap gap-1 mt-4">
    <template v-for="(link, index) in links" :key="index">
      <component
        :is="link.url ? Link : 'span'"
        :href="link.url || undefined"
        class="px-3 py-1 rounded border text-sm"
        :class="{
          'bg-blue-600 text-white': link.active,
          'text-gray-500 cursor-not-allowed': !link.url,
          'hover:bg-blue-100': link.url && !link.active
        }"
        v-html="link.label"
      />
    </template>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: Array
})
</script>

<style scoped>
/* Optionnel : tu peux styliser plus si tu veux */
</style>
