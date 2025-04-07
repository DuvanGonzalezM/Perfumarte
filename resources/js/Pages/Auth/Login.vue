<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useReCaptcha } from 'vue-recaptcha-v3';
import { useResponsive } from '@/Composables/useResponsive';

// Utilizamos el composable de responsividad
const { isMobile, isTablet } = useResponsive();

const form = useForm({
    username: '',
    password: '',
    captcha_token: '',
});

const submit = () => {
    form.post(route('login'));
};

const props = defineProps({
    recaptcha_site_key: { type: String },
});

const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
const recaptcha = async () => {
    try {
        await recaptchaLoaded()
        form.captcha_token = await executeRecaptcha('login');
        submit();
    } catch (error){
    }

};

</script>

<template>
    <GuestLayout :loading="form.processing ? true : false">

        <Head title="Inicio de Sesión" />
        <form @submit.prevent="recaptcha" :class="{ 'login-form-mobile': isMobile, 'login-form-tablet': isTablet }">
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
            <div class="text-red-600" v-if="form.errors.captcha_token">
                {{ form.errors.captcha_token }}
            </div>
            <div :class="[
                'd-flex justify-content-center',
                isMobile ? 'mt-4' : isTablet ? 'mt-4' : 'mt-5'
            ]">
                <PrimaryButton 
                    @click="recaptcha" 
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