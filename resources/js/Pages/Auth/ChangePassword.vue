<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    username: { type: String },
});
const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.put(route('password.update', props.username));
};
</script>

<template>
    <GuestLayout title="Cambiar Contraseña" :loading="form.processing">
        <Head title="Cambiar Contraseña" />
        <div class="text-center mb-1">
            <p>Su cuenta tiene una contraseña predeterminada. Por razones de seguridad, debe cambiarla antes de continuar.</p>
        </div>
        <form @submit.prevent="submit" class="form-container">
            <div class="form-group">
                <TextInput
                    labelValue="Nueva Contraseña"
                    id="password"
                    name="password"
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
                    name="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    :messageError="form.errors.password_confirmation"
                />
            </div>

            <div class="button-wrapper d-flex justify-content-center">
                <PrimaryButton 
                    @click="submit" 
                    :class="form.processing ? 'disabled' : ''"
                >
                Cambiar Contraseña
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>