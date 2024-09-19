<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('login'));
};
</script>

<template>


    <GuestLayout>
        <Head title="Inicio de Sesión" />
        <form @submit.prevent="submit">
            <div>
                <TextInput
                    labelValue="Nombre de usuario"
                    id="username"
                    name="username"
                    type="text"
                    v-model="form.username"
                    required
                />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="mt-4">
                <TextInput
                    labelValue="Contraseña"
                    id="password"
                    name="password"
                    type="password"
                    v-model="form.password"
                    autocomplete="current-password"
                    required
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="d-flex justify-content-center mt-5">
                <PrimaryButton type="submit" :disabled="form.processing">
                    Ingresar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>