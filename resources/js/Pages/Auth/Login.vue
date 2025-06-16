<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ButtonSubmit from '@/Components/ButtonSubmit.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useReCaptcha } from 'vue-recaptcha-v3';

const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();

const form = useForm({
    username: '',
    password: '',
    captcha_token: '',
});

const submit = async () => {
    await recaptchaLoaded();
    form.captcha_token = await executeRecaptcha('login');

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout :loading="form.processing ? true : false">

        <Head title="Inicio de Sesión" />
        <form @submit.prevent="submit" @keydown.enter.prevent="submit" class="form-container">
            <div class="form-group">
                <TextInput labelValue="Nombre de usuario" id="username" name="username" type="text"
                    v-model="form.username" :messageError="form.errors.username" required />
            </div>
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
                <ButtonSubmit :class="form.processing ? 'disabled' : ''">
                    Ingresar
                </ButtonSubmit>
            </div>
        </form>
    </GuestLayout>
</template>