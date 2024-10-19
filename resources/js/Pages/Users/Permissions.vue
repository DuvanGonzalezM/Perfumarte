<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { can } from 'laravel-permission-to-vuejs';
import ModalPrais from '@/Components/ModalPrais.vue';

const props = defineProps({
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

let methodPermission = 'create';
const form = useForm({
    name: null,
    id: null,
});

const editClick = (row) => {
    methodPermission = 'edit';
    form.name = row.name;
    form.id = row.id;
    showModal.value = true;
};

const createClick = () => {
    methodPermission = 'create';
    form.name = null;
    form.id = null;
    showModal.value = true;
};

const submit = () => {
    if (methodPermission == 'create') {
        form.post(route('permissions.store'));
    } else if (methodPermission == 'edit') {
        form.put(route('permissions.update', form.id));
    }
    showModal.value = false;
};
</script>

<template>

    <Head title="Permisos" />
    <BaseLayout>
        <template #header>
            <h1>Permisos</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Permisos</strong>
            </template>
            <div class="container">
                <PrimaryButton class="px-5" @click="createClick()" v-if="can('Crear Permisos')">
                    Agregar
                </PrimaryButton>
                <Table :data="permissions" :columns="columnsTable">
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
                        Permisos
                    </template>
                    <template #body>
                        <form @submit.prevent="submit">
                            <TextInput type="text" name="permission_name" id="permission_name" v-model="form.name"
                                :focus="form.name != null ? true : false" labelValue="Nombre del permiso"
                                :required="true" />
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