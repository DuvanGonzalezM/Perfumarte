<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useReCaptcha } from 'vue-recaptcha-v3';

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
    } catch (error) {
    }
};
</script>

<template>
    <GuestLayout :loading="form.processing ? true : false">

        <Head title="Inicio de Sesión" />
        <form @submit.prevent="recaptcha" @keydown.enter.prevent="recaptcha" class="form-container">
            <div class="form-group">
                <TextInput labelValue="Nombre de usuario" id="username" name="username" type="text"
                    v-model="form.username" :messageError="form.errors.username" required />
            </div>
            {{ console.log(form.errors.username, errors)
            }}
            <div class="form-group">
                <div class="input-wrapper">
                    <TextInput labelValue="Contraseña" id="password" name="password" type="password"
                        v-model="form.password" :messageError="form.errors.password" required />
                </div>
            </div>

            <div class="text-red-600" v-if="form.errors.captcha_token">
                {{ form.errors.captcha_token }}
            </div>

            <div class="button-wrapper d-flex justify-content-center">
                <PrimaryButton @click="recaptcha" :class="form.processing ? 'disabled' : ''">
                    Ingresar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>