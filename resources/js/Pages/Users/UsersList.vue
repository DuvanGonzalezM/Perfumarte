<script setup>
import InputError from '@/Components/InputError.vue';
import ModalPrais from '@/Components/ModalPrais.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
    },
    roles: {
        type: Array,
    },
    boss: {
        type: Array,
    }
});

const form = useForm({
    name: '',
    username: '',
    role_id: '',
    boss_user: '',
    location_id: 1,
    enabled: '',
    
});

const columnsTable = [
    {
        data: 'name',
        title: 'Nombre'
    },
    {
        data: "user_id",
        title: 'Roles / Permisos',
        render: function (data) {
            return '<a href="' + route("users.detail", data) + '"><i class="fa-solid fa-user-shield"></i></a>';
        }
    },
];

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]))
const optionsBoss = ref(props.boss.map((user) => [{ 'title': user.name, 'value': user.user_id }][0]))

const showModal = ref(false);
const openModal = () => {
    form.name = null;
    form.username = null;
    form.role_id = null;
    form.boss_user = null;
    form.enabled = null;
    showModal.value = true;
};
const submit = () => {
    form.post(route('users.store'));
    showModal.value = false;
};
</script>

<template>

    <Head title="Usuarios" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Usuarios</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Usuarios</strong>
            </template>
            <div class="container">
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('roles.list')" class="px-5">
                            Roles
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton :href="route('permissions.list')" class="px-5">
                            Permisos
                        </PrimaryButton>
                    </div>
                </div>
                <Table :data="users" :columns="columnsTable" />
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton @click="openModal" class="px-5">
                            <i class="fa-solid fa-user-plus"></i>
                        </PrimaryButton>
                    </div>
                </div>
                
                <ModalPrais v-model="showModal" @close="showModal = false">
                    <template #header>
                        Nuevo Usuario
                    </template>
                    <template #body>
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
                        </form>
                    </template>
                    <template #footer>
                        <PrimaryButton @click="submit" class="px-5">
                            Guardar
                        </PrimaryButton>
                    </template>
                </ModalPrais>
            </div>
        </SectionCard>
    </BaseLayout>
</template>