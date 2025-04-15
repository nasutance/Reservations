<template>
  <div v-if="bookable && representationsCount > 0">
    <button @click="handleClick" class="btn">
      Réserver
    </button>

    <LoginModal v-if="showModal" @close="showModal = false" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import LoginModal from '@/Components/LoginModal.vue'

const props = defineProps({
  showId: Number,
  bookable: Boolean,
  representationsCount: Number,
})

const showModal = ref(false)
const user = usePage().props.auth.user

function handleClick() {
  if (user) {
    router.get(route('reservation.create', { show: props.showId }))
  } else {
    showModal.value = true
  }
}
</script>

<style scoped>
.btn {
  padding: 0.5em 1em;
  background-color: #4f46e5;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
