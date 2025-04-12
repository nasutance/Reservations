<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
    <Head title="Accueil" />

    <div class="min-h-screen bg-white dark:bg-gray-900 flex flex-col items-center justify-center text-center p-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Bienvenue sur le site de réservation 🎭
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
            Connecte-toi pour découvrir les spectacles et réserver ta place.
        </p>

        <div class="flex gap-4">
            <Link
                href="/login"
                class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            >
                Connexion
            </Link>
            <Link
                href="/register"
                class="px-6 py-3 bg-gray-300 text-gray-900 rounded hover:bg-gray-400 transition"
            >
                Inscription
            </Link>
        </div>
    </div>
</template>
