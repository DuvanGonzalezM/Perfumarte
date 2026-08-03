<script setup>
import BaseLayout from '@/Layouts/BaseLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Alert from '@/Components/Alert.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('change-password.update'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <BaseLayout :loading="form.processing">
        <Head title="Cambiar Contraseña" />
        <template #header>
            <h1>Cambiar Contraseña</h1>
        </template>

        <Alert v-if="form.hasErrors" :message="Object.values(form.errors)[0]" type="danger" icon="fa-circle-xmark" />

        <form @submit.prevent="submit" class="form-container">
            <div class="form-group">
                <TextInput
                    labelValue="Contraseña Actual"
                    id="current_password"
                    name="current_password"
                    type="password"
                    v-model="form.current_password"
                    required
                />
            </div>

            <div class="form-group">
                <TextInput
                    labelValue="Nueva Contraseña"
                    id="password"
                    name="password"
                    type="password"
                    v-model="form.password"
                    required
                />
            </div>

            <div class="form-group">
                <TextInput
                    labelValue="Confirmar Nueva Contraseña"
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                />
            </div>

            <div class="button-wrapper d-flex justify-content-center">
                <PrimaryButton @click="submit" :class="form.processing ? 'disabled' : ''">
                    Cambiar Contraseña
                </PrimaryButton>
            </div>
        </form>
    </BaseLayout>
</template>
