<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionCard from '@/Components/SectionCard.vue';
import SelectSearch from '@/Components/SelectSearch.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';
import { onMounted, ref } from 'vue';
import { is, can } from 'laravel-permission-to-vuejs';

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
let myModal;
onMounted(() => {
    myModal = new Modal('#permission', {})
})

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
    myModal.show();
};

const createClick = () => {
    methodRole = 'create';
    form.name = null;
    form.id = null;
    form.permissions = null;
    myModal.show();
};

const closeModal = () => {
    myModal.hide();
};

const submit = () => {
    if(methodRole == 'create'){
        form.post(route('roles.store'));
    } else if(methodRole == 'edit') {
        form.put(route('roles.update', form.id));
    }
    myModal.hide();
};

const optionsPermission = ref(props.permissions.map((permission) => [{ 'title': permission.name, 'value': permission.id }][0]));
</script>

<template>

    <Head title="Roles" />
    <BaseLayout>
        <template #header>
            <h1>Roles</h1>
        </template>
        <SectionCard>
            <template #headerSection>
                <strong>Roles</strong>
            </template>
            <div class="container">
                <PrimaryButton class="px-5" @click="createClick()">
                    Agregar
                </PrimaryButton>
                <Table :data="roles" :columns="columnsTable">
                    <template #templateRender="items">
                        <a href="#" @click="editClick(items.item.rowData)"> <i class="fa-solid fa-pen-to-square"></i> </a>
                    </template>
                </Table>
                <div class="row my-5 text-center">
                    <div class="col">
                        <PrimaryButton :href="route('users.list')" class="px-5">
                            Volver
                        </PrimaryButton>
                    </div>
                </div>
                <div class="modal fade" id="permission" tabindex="-1" aria-labelledby="Permission" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="Permission">Crear Permiso</h1>
                                <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body pt-4">
                                <form @submit.prevent="submit">
                                    <TextInput type="text" name="permission_name" id="permission_name"
                                        v-model="form.name" :focus="form.name != null ? true : false" labelValue="Nombre del permiso" :required="true" />
                                    
                                    <SelectSearch :multiple="true" class="mt-5" v-model="form.permissions" :options="optionsPermission" labelValue="Permisos" />
                                </form>
                            </div>
                            <div class="modal-footer">
                                <PrimaryButton @click="submit" class="px-5">
                                    Guardar
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </SectionCard>
    </BaseLayout>
</template>