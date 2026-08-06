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
import { computed, ref, watch } from 'vue';
import { is } from 'laravel-permission-to-vuejs';

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
    },
    hierarchy: {
        type: Object,
        default: () => ({ bossRole: {}, cashRegisterRoles: [], zoneRoles: [] }),
    }
});


const form = useForm({
    name: '',
    username: '',
    role_id: '',
    boss_user: '',
    zone_id: null,
    enabled: '',
});

const optionsRoles = ref(props.roles.map((rol) => [{ 'title': rol.name, 'value': rol.id }][0]))
const optionsZones = ref(props.zones.map((zone) => [{ 'title': zone.zone_name, 'value': zone.zone_id }][0]))

// La jerarquía llega del backend (config/prais.php). El rol elegido decide si la
// cuenta depende de un jefe, y de qué rol debe ser ese jefe.
const requiredBoss = computed(() => props.hierarchy.bossRole?.[form.role_id] ?? null);

const bossOptions = computed(() => {
    if (!requiredBoss.value) {
        return [];
    }

    return props.boss
        .filter((user) => user.roles?.some((role) => role.id === requiredBoss.value.role_id))
        .map((user) => ({
            'title': `${user.name} - Zona: ${user.zone_id ?? 'Sin zona'}`,
            'value': user.user_id
        }));
});

// No hay a quién colgar la cuenta: se avisa y se bloquea el envío.
const missingBoss = computed(() => requiredBoss.value !== null && bossOptions.value.length === 0);

const needsCashRegister = computed(() => props.hierarchy.cashRegisterRoles?.includes(form.role_id));
const needsZone = computed(() => props.hierarchy.zoneRoles?.includes(form.role_id));

// Al cambiar de rol el jefe anterior deja de ser válido.
watch(() => form.role_id, () => {
    form.boss_user = '';
});
const showModal = ref(false);
const showSuccessCreateModal = ref(null);

// Campos que el formulario alcanza a pintar según el rol elegido. Todo error de
// validación fuera de esta lista se muestra al pie del modal: de lo contrario el
// backend rechaza la creación y el usuario no ve ningún mensaje.
const shownFields = computed(() => {
    const fields = ['name', 'username', 'role_id'];
    if (requiredBoss.value) fields.push('boss_user');
    if (needsCashRegister.value) fields.push('enabled');
    else if (needsZone.value) fields.push('zone_id');
    return fields;
});

const unhandledErrors = computed(() => Object.entries(form.errors)
    .filter(([field]) => !shownFields.value.includes(field))
    .map(([, message]) => message));

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
        title: 'Nombre',
        render: function (data, type, row) {
            const opacity = row.deleted_at ? 'opacity: 0.5;' : '';
            return `<span style="${opacity}">${data}</span>`;
        }
    },
    {
        data: 'username',
        title: 'Documento de usuario',
        render: function (data, type, row) {
            const opacity = row.deleted_at ? 'opacity: 0.5;' : '';
            return `<span style="${opacity}">${data}</span>`;
        }
    },
    {
        data: 'roles',
        title: 'Roles',
        render: function (data, type, row) {
            const opacity = row.deleted_at ? 'opacity: 0.5;' : '';
            return `<span style="${opacity}">${data.map(role => role.name).join(', ')}</span>`;
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
                        <PrimaryButton :href="route('roles.list')" v-if="is('TI')" class="px-5">
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

                <div class="mt-4" v-if="requiredBoss">
                    <SelectSearch v-if="!missingBoss" v-model="form.boss_user" :options="bossOptions"
                        :labelValue="`Jefe (${requiredBoss.role_name})`" required />
                    <InputError v-else
                        :message="`El rol seleccionado depende de un ${requiredBoss.role_name} y todavía no existe ninguna cuenta con ese rol. Créela primero.`" />
                    <InputError class="mt-2" :message="form.errors.boss_user" />
                </div>
                <div class="mt-4" v-if="needsCashRegister">
                    <label :for="enabled">¿Utilizara la caja? </label>
                    <input id="enabled" type="checkbox" v-model="form.enabled" />
                    <InputError class="mt-2" :message="form.errors.enabled" />
                </div>
                <div class="mt-4" v-else-if="needsZone">
                    <SelectSearch v-model="form.zone_id" :options="optionsZones" labelValue="Zona"
                        required />
                    <InputError class="mt-2" :message="form.errors.zone_id" />
                </div>

                <div class="mt-4" v-if="unhandledErrors.length">
                    <InputError v-for="(message, index) in unhandledErrors" :key="index" :message="message" />
                </div>
            </form>
        </template>
        <template #footer>
            <PrimaryButton @click="submit" class="px-5" :disabled="missingBoss">
                Crear
            </PrimaryButton>
            <PrimaryButton @click="showModal = false" class="px-5">
                Cancelar
            </PrimaryButton>
        </template>
    </ModalPrais>
</template>