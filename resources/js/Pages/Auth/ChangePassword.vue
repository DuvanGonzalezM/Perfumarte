<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout title="Cambiar Contraseña">
        <Head title="Cambiar Contraseña" />

        <form @submit.prevent="submit" class="form-container">
            <div class="text-center mb-4">
                <p>Su cuenta tiene una contraseña predeterminada. Por razones de seguridad, debe cambiarla antes de continuar.</p>
            </div>

            <div class="form-group">
                <TextInput
                    labelValue="Contraseña Actual"
                    id="current_password"
                    type="password"
                    v-model="form.current_password"
                    required
                    :messageError="form.errors.current_password"
                />
            </div>

            <div class="form-group">
                <TextInput
                    labelValue="Nueva Contraseña"
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    :messageError="form.errors.password"
                />
            </div>

            <div class="form-group">
                <TextInput
                    labelValue="Confirmar Nueva Contraseña"
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    :messageError="form.errors.password_confirmation"
                />
            </div>

            <div class="button-wrapper d-flex justify-content-center">
                <PrimaryButton 
                    :class="form.processing ? 'disabled' : ''"
                    :disabled="form.processing"
                >
                    Cambiar Contraseña
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>