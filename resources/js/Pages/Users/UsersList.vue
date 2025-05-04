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
    },
    zones: {
        type: Array,
    }
});


const form = useForm({
    name: '',
    username: '',
    role_id: '',
    boss_user: '',
    location_id: 1,
    zone_id: null,
    enabled: '',
});

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]))
const optionsBoss = ref(props.boss.map((user) => ({
    'title': `${user.roles?.[0]?.name || 'Sin rol'} - ${user.name} - Zona: ${user.zone_id ?? 'Sin zona'}`,
    'value': user.user_id
})))
const optionsZones = ref(props.zones.map((zone) => [{ 'title': zone.zone_name, 'value': zone.zone_id }][0]))
const showModal = ref(false);
const showSuccessCreateModal = ref(null);

const openModal = () => {
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            showSuccessCreateModal.value = true;
            showModal.value = false;
        },
        onError: (errors) => {
            
        }
    });
};

const columnsTable = [
    {
        data: 'name',
        title: 'Nombre'
    },
    {
        data: 'username',
        title: 'Documento de usuario'
    },
    {
        data: 'roles',
        title: 'Roles',
        render: function (data) {
            return data.map(role => role.name).join(', ');
        },
    },
    {
        data: "user_id",
        title: 'Detalle',
        render: function (data) {
            return '<a href="' + route("users.detail", data) + '"><i class="fa-solid fa-user-shield"></i></a>';
        }
    }
];

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
                <div class="row">
                    <div class="col-md-6 col-12">
                        <PrimaryButton @click="openModal" class="px-5">
                            Nuevo Usuario
                        </PrimaryButton>
                    </div>
                    <div class="col-md-6 col-12 text-end">
                        <PrimaryButton :href="route('roles.list')" class="px-5">
                            Roles
                        </PrimaryButton>
                    </div>
                </div>
            </div>
            <Table :columns="columnsTable" :data="props.users" />
        </SectionCard>
    </BaseLayout>

    <ModalPrais v-model="showSuccessCreateModal" @close="showSuccessCreateModal = false">
        <template #header>
            Crear Usuario
        </template>
        <template #body>
            <div class="text-center">
                <i class="fa-solid fa-check text-success"></i>
                <h3>Usuario creado con éxito</h3>
            </div>
        </template>
    </ModalPrais>

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
                    <TextInput labelValue="Documento de usuario" id="username" type="text" v-model="form.username" required
                        autocomplete="username" />
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <div class="mt-4">
                    <SelectSearch v-model="form.role_id" :options="optionsRoles" labelValue="Rol"
                        required />
                    <InputError class="mt-2" :message="form.errors.role_id" />
                </div>

                <div class="mt-4" v-if="[4, 5, 6].includes(form.role_id)">
                    <SelectSearch v-model="form.boss_user" :options="optionsBoss" labelValue="Jefe"
                        required />
                    <InputError class="mt-2" :message="form.errors.boss_user" />
                </div>
                <div class="mt-4" v-if="[5].includes(form.role_id)">
                    <label :for="enabled">¿Utilizara la caja? </label>
                    <input id="enabled" type="checkbox" v-model="form.enabled" required />
                    <InputError class="mt-2" :message="form.errors.enabled" />
                </div>
                <div class="mt-4" v-else-if="[7].includes(form.role_id)">
                    <SelectSearch v-model="form.zone_id" :options="optionsZones" labelValue="Zona"
                        required />
                    <InputError class="mt-2" :message="form.errors.zone_id" />
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="submit" class="px-5">
                Crear
            </PrimaryButton>
            <PrimaryButton @click="showModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>
</template>