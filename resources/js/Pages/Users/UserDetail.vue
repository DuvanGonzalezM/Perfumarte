<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    user: {
        type: Object,
    },
    roles: {
        type: Array,
    },
    permissions: {
        type: Array,
    },
    zones: {
        type: Array,
    },
    boss: {
        type: Array,
    }
});

const rolesIdUser = props.user.roles.length > 0 ? props.user.roles.map((role) => role.id) : null;
const permissionsIdUser = props.user.permissions.length > 0 ? props.user.permissions.map((permission) => permission.id) : null;
const optionsBoss = ref(props.boss.map((user_boss) => [{ 'title': user_boss.name, 'value': user_boss.user_id }][0]));
const optionsZones = ref(props.zones.map((zone) => [{ 'title': zone.zone_name, 'value': zone.zone_id }][0]));
const showConfirmEditModal = ref(null);
const showConfirmDeleteModal = ref(null);
let permissionsNameRole = [];
const form = useForm({
    name: props.user.name,
    username: props.user.username,
    boss_user: props.user.boss_user != 0 ? parseInt(props.user.boss_user) : null,
    location_id: 1,
    zone_id: props.user.zone_id != 0 ? parseInt(props.user.zone_id) : null,
    enabled: props.user.enabled,
    roles: rolesIdUser,
    permissions: permissionsIdUser,
});

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]));
const optionsPermission = ref(props.permissions.map((permission) => [{ 'title': permission.name, 'value': permission.id }][0]));

if (form.roles) {
    let rolesUser = props.roles.filter(role => form.roles.includes(role.id));
    if (rolesUser.length > 0) {
        rolesUser.map((role) => role.permissions.length > 0 ? role.permissions.map((permission) => permissionsNameRole.push(permission.name)) : null);
        optionsPermission.value = optionsPermission.value.filter(item => !permissionsNameRole.some(permission => permission === item.title));
    }
}

const submit = () => {
    form.post(route('users.role_permi', props.user.user_id));
};

const selectedRoles = async () => {
    try {
        form.permissions = null;
        if (form.roles) {
            let rolesUser = props.roles.filter(role => form.roles.includes(role.id));
            optionsPermission.value = props.permissions.map((permission) => [{ 'title': permission.name, 'value': permission.id }][0]);
            permissionsNameRole = [];
            if (rolesUser.length > 0) {
                rolesUser.map((role) => role.permissions.length > 0 ? role.permissions.map((permission) => permissionsNameRole.push(permission.name)) : null);
                optionsPermission.value = optionsPermission.value.filter(item => !permissionsNameRole.some(permission => permission === item.title));
            }
        }

    } catch (error) {
        form.permissions = null;
    }
}

const deleteUser = () => {
    form.delete(route('users.destroy', { user_id: props.user.user_id }), {
        onSuccess: () => {
            showConfirmDeleteModal.value = false;
        },
    });
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
                <strong>{{ props.user.name }}</strong>
            </template>
            <div class="row my-5">
                <div class="col-12 text-end">
                    <PrimaryButton @click="showConfirmDeleteModal = true;" class="px-5">
                        Eliminar
                    </PrimaryButton>
                </div>
            </div>
            <form @submit.prevent="submit" class="table-prais">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <TextInput labelValue="Nombre" id="edit-name" name="edit-name" type="text" v-model="form.name"
                            required autofocus autocomplete="name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="col-md-6 mb-4">
                        <TextInput labelValue="Documento de usuario" id="edit-username" name="edit-username" type="text"
                            v-model="form.username" required autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>
                    <div class="col-md-6 mb-4"
                        v-if="[4, 5, 6].find(role => form.roles.includes(role)) && ![7].find(role => form.roles.includes(role))">
                        <SelectSearch v-model="form.boss_user" :options="optionsBoss" labelValue="Jefe" required />
                        <InputError class="mt-2" :message="form.errors.boss_user" />
                    </div>
                    <div class="col-md-6 mb-4" v-if="[5].find(role => form.roles.includes(role))">
                        <label :for="enabled">¿Utilizara la caja? </label>
                        <input id="enabled" name="enabled" type="checkbox" v-model="form.enabled" required />
                        <InputError class="mt-2" :message="form.errors.enabled" />
                    </div>
                    <div class="col-md-6 mb-4" v-if="[7].find(role => form.roles.includes(role))">
                        <SelectSearch v-model="form.zone_id" :options="optionsZones" labelValue="Zona" required />
                        <InputError class="mt-2" :message="form.errors.zone_id" />
                    </div>
                </div>
                <div class="row">
                    <h4>Roles</h4>
                    <div class="col pb-5 align-middle">
                        <SelectSearch :multiple="true" :changeFunction="selectedRoles" v-model="form.roles"
                            :options="optionsRoles" />
                    </div>
                </div>

                <div class="row">
                    <h4>Permisos</h4>
                    <span>{{ permissionsNameRole.toString().replaceAll(',', ' - ') }}</span>
                    <div class="col mb-5 align-middle">
                        <SelectSearch :multiple="true" v-model="form.permissions" :options="optionsPermission" />
                    </div>
                </div>
                <div class="row my-5">
                    <div class="col-6">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                    <div class="col-6 text-end">
                        <PrimaryButton @click="showConfirmEditModal = true;" class="px-5">
                            Enviar
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </SectionCard>

        <ModalPrais v-model="showConfirmEditModal" @close="showConfirmEditModal = false">
            <template #header>
                Confirmar Edición
            </template>
            <template #body>
                <div class="text-center">
                    <i class="fa-solid fa-check text-success"></i>
                    <h3>¿Estás seguro de que quieres editar este usuario?</h3>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="submit()" class="px-5">
                    Confirmar
                </PrimaryButton>
                <PrimaryButton @click="showConfirmEditModal = false" class="px-5">
                    Cancelar
                </PrimaryButton>
            </template>
        </ModalPrais>

        <ModalPrais v-model="showConfirmDeleteModal" @close="showConfirmDeleteModal = false">
            <template #header>
                Confirmar Eliminación
            </template>
            <template #body>
                <div class="text-center">
                    <i class="fa-solid fa-trash text-danger"></i>
                    <h3>¿Estás seguro de que quieres eliminar este usuario?</h3>
                </div>
            </template>
            <template #footer>
                <PrimaryButton @click="deleteUser()" class="px-5">
                    Confirmar
                </PrimaryButton>
                <PrimaryButton @click="showConfirmDeleteModal = false" class="px-5">
                    Cancelar
                </PrimaryButton>
            </template>
        </ModalPrais>
    </BaseLayout>
</template>