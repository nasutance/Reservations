<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <h2 class="text-xl font-bold mb-4">Connexion requise</h2>
      <LoginForm @cancel="$emit('close')" />
    </div>
  </div>
</template>

<script setup>
import LoginForm from '@/Components/LoginForm.vue'
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 50;
}
.modal {
  background: white;
  padding: 2em;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
</style>
