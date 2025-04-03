<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SelectSearch from '@/Components/SelectSearch.vue';
import { ref } from 'vue';
 

const props = defineProps({
    roles: Array,
    boss: Array
})

const form = useForm({
    name: '',
    username: '',
    password: '',
    role_id: '',
    boss_user: '',
    location_id: 1,
    enabled: '',
    
});
const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]))
const optionsBoss = ref(props.boss.map((user) => [{ 'title': user.name, 'value': user.user_id }][0]))

const submit = () => {
    form.post(route('register'));
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <TextInput labelValue="Nombre" id="name" type="text" v-model="form.name" required autofocus
                    autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <TextInput labelValue="Nombre de usuario" id="username" type="text" v-model="form.username" required
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="mt-4">
                <SelectSearch v-model="form.role_id" :options="optionsRoles" labelValue="Rol"
                    required />
                <InputError class="mt-2" :message="form.errors.role_id" />
            </div>

            <div class="mt-4">
                <SelectSearch v-model="form.boss_user" :options="optionsBoss" labelValue="Jefe"
                    required />
                <InputError class="mt-2" :message="form.errors.boss_user" />
            </div>

            <!-- <div class="mt-4">
                <TextInput labelValue="Contraseña" id="password" name="password" type="password" v-model="form.password"
                    autocomplete="current-password" required />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <TextInput labelValue="Confirmar Contraseña" id="password_confirmation" name="password_confirmation"
                    type="password" v-model="form.password_confirmation" autocomplete="new-password" required />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div> -->

            <div class="mt-4">
                <label :for="enabled">¿Utilizara la caja? </label>
                <input id="enabled" type="checkbox" v-model="form.enabled" required />
                <InputError class="mt-2" :message="form.errors.enabled" />
            </div>

            <div class="flex items-center justify-end mt-4">


                <PrimaryButton @click="submit" :disabled="form.processing">
                    Registrar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
