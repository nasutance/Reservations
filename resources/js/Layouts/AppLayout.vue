<template>
  <div class="min-h-screen bg-gray-100">
    <Banner />
    <AuthenticatedNav />

    <main class="max-w-5xl mx-auto p-6">
      <slot />
    </main>
  </div>
</template>

<script setup>
import Banner from '@/Components/Banner.vue'
import AuthenticatedNav from '@/Components/AuthenticatedNav.vue'
</script>
