<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useResponsive } from '@/Composables/useResponsive';

// Utilizamos el composable de responsividad
const { isMobile, isTablet, isDesktop } = useResponsive();

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
        <form @submit.prevent="submit" :class="{ 'login-form-mobile': isMobile, 'login-form-tablet': isTablet }">
            <div :class="{ 'mb-3': isMobile }">
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

            <div :class="[
                'mt-4', 
                { 'mt-3': isMobile }
            ]">
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
            <div :class="[
                'd-flex justify-content-center',
                isMobile ? 'mt-4' : isTablet ? 'mt-4' : 'mt-5'
            ]">
                <PrimaryButton 
                    @click="submit" 
                    :class="[
                        form.processing ? 'disabled' : '',
                        { 'btn-mobile': isMobile, 'btn-tablet': isTablet }
                    ]"
                >
                    Ingresar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>