<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    firstname: user.firstname,
    lastname: user.lastname,
    email: user.email,
    login: user.login,
    langue: user.langue || 'fr',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Données personnelles
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Mettez à jour vos données et votre adresse email.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <!-- Champ pour le nom -->
            <div>
                <InputLabel for="firstname" value="Prénom" />
                <TextInput
                    id="firstname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.firstname"
                    required
                    autofocus
                    autocomplete="firstname"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="lastname" value="Nom" />
                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastname"
                    required
                    autofocus
                    autocomplete="lastname"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <!-- Champ pour le login -->
            <div>
                <InputLabel for="login" value="Login" />
                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.login" />
            </div>

            <!-- Champ pour la langue -->
            <div>
                <InputLabel for="langue" value="Langue" />
                <select
                    id="langue"
                    class="mt-1 block w-full"
                    v-model="form.langue"
                >
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                    <option value="nl">Nederlands</option>
                    <option value="it">Italiano</option>
                    <option value="de">Deutsch</option>
                </select>
                <InputError class="mt-2" :message="form.errors.langue" />
            </div>

            <!-- Champ pour l'email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Bouton de sauvegarde -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
