<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ButtonSubmit from '@/Components/ButtonSubmit.vue';
import TextInput from '@/Components/TextInput.vue';
import Alert from '@/Components/Alert.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useReCaptcha } from 'vue-recaptcha-v3';

const { executeRecaptcha, recaptchaLoaded } = useReCaptcha();
const props = defineProps({
    status: {
        type: String,
    },
    error: {
        type: String,
    },
});

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
        <Alert v-if="props.error || form.hasErrors" :message="props.error ? props.error : Object.values(form.errors)[0]" type="danger" icon="fa-circle-xmark" />
        <Head title="Inicio de Sesión" />
        <form @submit.prevent="submit" @keydown.enter.prevent="submit" class="form-container">
            <div class="form-group">
                <TextInput labelValue="Nombre de usuario" id="username" name="username" type="text"
                    v-model="form.username" required />
            </div>
            <div class="form-group">
                <div class="input-wrapper">
                    <TextInput labelValue="Contraseña" id="password" name="password" type="password"
                        v-model="form.password" required />
                </div>
            </div>
            <div class="button-wrapper d-flex justify-content-center">
                <ButtonSubmit :class="form.processing ? 'disabled' : ''">
                    Ingresar
                </ButtonSubmit>
            </div>
        </form>
    </GuestLayout>
</template>