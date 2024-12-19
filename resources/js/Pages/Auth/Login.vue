<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    username: '',
    password: '',
});

const submit = () => {
    form.post(route('login'));
};
</script>

<template>


    <GuestLayout :loading="form.processing ? true : false">
        <Head title="Inicio de Sesión" />
        <form @submit.prevent="submit">
            <div>
                <TextInput
                    labelValue="Nombre de usuario"
                    id="username"
                    name="username"
                    type="text"
                    v-model="form.username"
                    :messageError="form.errors.username"
                    required
                />
            </div>

            <div class="mt-4">
                <TextInput
                    labelValue="Contraseña"
                    id="password"
                    name="password"
                    type="password"
                    v-model="form.password"
                    :messageError="form.errors.password"
                    required
                />
            </div>
            <div class="d-flex justify-content-center mt-5">
                <PrimaryButton @click="submit" :class="form.processing ? 'disabled' : ''">
                    Ingresar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>