<template>
  <form @submit.prevent="submit">
    <div class="mb-4">
      <InputLabel for="email" value="Email" />
      <TextInput
        id="email"
        type="email"
        class="mt-1 block w-full"
        v-model="form.email"
        required
        autofocus
        autocomplete="username"
      />
      <InputError class="mt-2" :message="form.errors.email" />
    </div>

    <div class="mb-4">
      <InputLabel for="password" value="Mot de passe" />
      <TextInput
        id="password"
        type="password"
        class="mt-1 block w-full"
        v-model="form.password"
        required
        autocomplete="current-password"
      />
      <InputError class="mt-2" :message="form.errors.password" />
    </div>

    <div class="mt-4 flex justify-end">
      <PrimaryButton class="me-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Se connecter
      </PrimaryButton>
      <button type="button" @click="$emit('cancel')" class="btn-secondary">Annuler</button>
    </div>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const emit = defineEmits(['cancel'])

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<style scoped>
.btn-secondary {
  background-color: #e5e7eb;
  color: #374151;
  padding: 0.5em 1em;
  border-radius: 4px;
}
</style>
