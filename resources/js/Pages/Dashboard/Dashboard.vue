<template>
  <Head title="Mon espace" />
  <AppLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 leading-tight">Mon espace</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <AdminDashboard v-if="isAdmin" />
        <MemberDashboard v-else />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminDashboard from '@/Pages/Dashboard/AdminDashboard.vue'
import MemberDashboard from '@/Pages/Dashboard/MemberDashboard.vue'

const user = usePage().props.auth?.user || {}
const isAdmin = user?.roles?.some(role => role.role === 'admin')
</script>
