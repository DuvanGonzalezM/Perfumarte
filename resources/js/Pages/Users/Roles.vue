<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { can } from 'laravel-permission-to-vuejs';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
    roles: {
        type: Array,
    },
    permissions: {
        type: Array,
    },
});

const columnsTable = [
    {
        data: 'name',
        title: 'Nombre'
    },
    {
        data: 'name',
        title: 'Editar',
        render: '#render',
    },
];

const showModal = ref(false);

let methodRole = 'create';
const form = useForm({
    name: null,
    id: null,
    permissions: null
});

const editClick = (row) => {
    methodRole = 'edit';
    form.name = row.name;
    form.id = row.id;
    form.permissions = row.permissions.length > 0 ? row.permissions.map((permission) => permission.id) : null;
    showModal.value = true;
};

const createClick = () => {
    methodRole = 'create';
    form.name = null;
    form.id = null;
    form.permissions = null;
    showModal.value = true;
};

const submit = () => {
    if (methodRole == 'create') {
        form.post(route('roles.store'));
    } else if (methodRole == 'edit') {
        form.put(route('roles.update', form.id));
    }
    showModal.value = false;
};

const optionsPermission = ref(props.permissions.map((permission) => ({ 'title': permission.name, 'value': permission.id })));
</script>

<template>

    <Head title="Roles" />
    <BaseLayout :loading="form.processing ? true : false">
        <template #header>
            <h1>Roles</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Roles</strong>
            </template>
            <div class="container">
                <PrimaryButton class="px-5" @click="createClick()" v-if="can('Crear Roles')">
                    Agregar
                </PrimaryButton>
                <Table :data="roles" :columns="columnsTable">
                    <template #templateRender="items">
                        <a href="#" @click="editClick(items.item.rowData)"> <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </template>
                </Table>
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
                <ModalPrais v-model="showModal" @close="showModal = false">
                    <template #header>
                        Roles
                    </template>
                    <template #body>
                        <form @submit.prevent="submit">
                            <TextInput type="text" name="roles_name" id="roles_name" v-model="form.name"
                                :focus="form.name != null ? true : false" labelValue="Nombre del permiso"
                                :required="true" />

                            <SelectSearch :multiple="true" class="mt-5" v-model="form.permissions"
                                :options="optionsPermission" labelValue="Permisos" />
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